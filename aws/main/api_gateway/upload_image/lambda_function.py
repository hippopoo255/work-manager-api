from posix import environ
import boto3
import json
import urllib.parse
import base64
import io
import os
from cgi import FieldStorage

class InvalidError(Exception):
  def __init__(self, value):
    self.value = value

  def __str__(self):
    return repr(self.value)

bucket_name = 'asset.work-manager.site'
s3 = boto3.resource('s3')
bucket = s3.Bucket(bucket_name)

dynamodb = boto3.resource('dynamodb')
sequences_table = dynamodb.Table('sequences')

def lambda_handler(event, context):
  print(json.dumps(event))
  dir_name = event['context']['resource-path'].lstrip('/')
  fs = get_file_req(event)
  print('-------')
  print('fs:{}'.format(fs))
  print('-------')
  # CORSチェック
  request_origin = event['params']['header']['origin']
  print('request_origin:{}'.format(request_origin))
  if is_white_origin(request_origin):
    try:
      path_list = []
      # 画像のアップロード
      for f in fs.list:
        file_name = f.filename
        result = file_upload(f, get_relative_path(dir_name, file_name))
        if result:
          path_list.append(result)
      
      response_body = {
        'src': path_list
      }
      
      return response(request_origin, response_body)

    except InvalidError as e:
      print('Invalid Error!!')
      show_err_log(e)
      response_body = {
        'message': e
      }
      return response(request_origin, response_body, 422)

    except Exception as e:
      print('Server Error!!')
      show_err_log(e)
      response_body = {
        'message': e
      }
      return response(request_origin, response_body, 500)

  else:
    response_body = {
      "message": 'access denied...',
    }
    return response(request_origin, response_body, 403)

def is_white_origin(origin_name):
  origin_white_list = os.environ['ORIGIN_WHITE_LIST'].split(',')
  return origin_name in origin_white_list

def get_file_req(event):
  fp = io.BytesIO(base64.b64decode(event['body-json']))
  headers = event['params']['header']
  environ = {'REQUEST_METHOD': 'POST'}
  fs = FieldStorage(fp=fp, environ=environ, headers=headers)
  return fs

def is_image_type(file_type):
  permitted_list = ['image/png', 'image/png', 'image/jpeg', 'image/svg+xml', 'image/gif']
  return file_type in permitted_list

def get_relative_path(dir_name, file_name):
  file_name = random_prefix() + file_name
  return '{}/{}'.format(dir_name, file_name)

def random_prefix():
  # テーブル内の項目更新
  response = sequences_table.update_item(
    Key={
      'table_name': 's3_blog_assets'
    },
    UpdateExpression="set seq = seq + :val",
    ExpressionAttributeValues={
      ':val': 1
    },
    ReturnValues='UPDATED_NEW'
  )
  
  return '{}_'.format(str(response['Attributes']['seq']))

def uploaded_image_url(relative_path):
  storage_url = os.environ['STORAGE_URL'] if 'STORAGE_URL' in os.environ else ''
  return '{}/{}'.format(storage_url, relative_path)

def file_upload(f, s3_key):
  print(f.filename, f.type, f.value)
  if not is_image_type(f.type):
    raise InvalidError('PNG、JPEG、GIF、SVG形式を選択してください')

  bucket.put_object(
    Body = f.value,
    Key = s3_key
  )
  return uploaded_image_url(s3_key)

def show_err_log(e):
  print('============')
  print(e)
  print('============')

def response(request_origin, response_body, status_code = 200):
  return {
    'statusCode': status_code,  # 201, 204...etc
    'headers': {
      'content-type': 'application/json',
      'Access-Control-Allow-Headers': 'Content-Type,X-Amz-Date,Authorization,X-Api-Key,X-Amz-Security-Token',
      'Access-Control-Allow-Origin': request_origin,
      'Access-Control-Allow-Methods': 'OPTIONS,POST',
      'Access-Control-Allow-Credentials': 'true',
    },
    'body': response_body
  }

  # print(req)
  # dir_name = event['context']['resource-path']
  # print(dir_name)
  # image_body = base64.b64decode(event['body-json'])
  # binary_arr = image_body.split(b'\r\n')
  # print(binary_arr[5].decode())
  # for index, val in enumerate(binary_arr):
  #   if index == 3:
  #     image_binary_data = val.split(b'base64,')
  #     print(image_binary_data)
  #     # 拡張子の取得
  #     ext = get_extension(image_binary_data[0].decode())
  #     print('extension:{}'.format(ext))
  #     # 画像データのみ
  #     print('------画像データか？------', end='')
  #     print(ext_permission(ext))
  #     if ext_permission(ext):
  #       bucket.put_object(
  #         Body = base64.b64decode(image_binary_data[1]),
  #         Key = '{}/uidayo.{}'.format(dir_name, ext) 
  #       )