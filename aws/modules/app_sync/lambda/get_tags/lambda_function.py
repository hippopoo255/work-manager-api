import boto3
import json

class InvalidError(Exception):
  def __init__(self, value):
    self.value = value

  def __str__(self):
    return repr(self.value)

dynamodb = boto3.resource('dynamodb')
tags_table = dynamodb.Table('tags')

def lambda_handler(event, context):
  print_log('context', json.dumps(event))

  try:
    response = tags_table.scan()

    print_log('tag_scan_response', json.dumps(response))

    return response.get('Items', [])

  except InvalidError as e:
      print('Invalid Error!!')
      show_err_log(e)

  except Exception as e:
    print('Server Error!!')
    show_err_log(e)

def show_err_log(e):
  print('============')
  print(e)
  print('============')

def print_log(title, body):
  print('================')
  print('%s' % (title))
  print(body)
  print('================')