import boto3
from boto3.dynamodb.conditions import Key
import json
import datetime

dynamodb = boto3.resource('dynamodb')
tags_table = dynamodb.Table('tags')
sequences_table = dynamodb.Table('sequences')
blogs_table = dynamodb.Table('blogs')

def latest_item_id(table, table_name):
  # テーブル内の項目更新
  response = table.update_item(
    Key={
      'table_name': table_name
    },
    UpdateExpression="set seq = seq + :val",
    ExpressionAttributeValues={
      ':val': 1
    },
    ReturnValues='UPDATED_NEW'
  )
  # 16桁ゼロ詰めの文字列を返す
  return str(response['Attributes']['seq']).zfill(16)

def now_str():
  now = datetime.datetime.now()
  return now.strftime("%Y-%m-%dT%H:%MZ")

def create_tags(tags):
  response = []
  now = now_str()

  for tag in tags:
    if not 'id' in tag:
      tag['id'] = latest_item_id(sequences_table, 'tags')
      tags_table.update_item(
        Key={
          'id': tag['id']
        },
        UpdateExpression="set #name = :name, #created_at = :created_at, #updated_at = :updated_at",
        ExpressionAttributeNames={
          '#name': 'name',
          '#created_at': 'created_at',
          '#updated_at': 'updated_at',
        },
        ExpressionAttributeValues={
          ':name': tag['name'],
          ':created_at': now,
          ':updated_at': now,
        },
        ReturnValues='UPDATED_NEW'
      )
    response.append(tag)

  print("tags created!")
  print(json.dumps(response))
  return response



def lambda_handler(event, context):
  # リクエストボディをディクショナリにする
  args = eval(json.dumps(event['arguments']))
  request_inputs = args['input']
  blog_id = args['id'] if 'id' in args else latest_item_id(sequences_table, 'blogs')

  try:
    tags = create_tags(request_inputs['tags'])
    now = now_str()
    updateExpression = "set #title = :title, #body = :body, #status = :status, #createdBy = :createdBy, #tags = :tags, #updated_at = :updated_at, #created_at = "
    expressionAttributeNames = {
      '#title': 'title',
      '#body': 'body',
      '#status': 'status',
      '#createdBy': 'createdBy',
      '#tags': 'tags',
      '#updated_at': 'updated_at', 
      '#created_at': 'created_at', 
    }
    expressionAttributeValues = {
      ':title': request_inputs['title'],
      ':body': request_inputs['body'],
      ':status': request_inputs['status'],
      ':createdBy': request_inputs['createdBy'],
      ':tags': tags,
      ':updated_at': now,
    }

    if not 'id' in args:
      expressionAttributeValues[':created_at'] = now
      
    updateExpression += "created_at" if 'id' in args else ":created_at"

    response = blogs_table.update_item(
      Key={
        'id': blog_id
      },
      UpdateExpression=updateExpression,
      ExpressionAttributeNames=expressionAttributeNames,
      ExpressionAttributeValues=expressionAttributeValues,
      ReturnValues='UPDATED_NEW'
    )
    print("blog created!")
    result = response['Attributes']
    result['id'] = blog_id
    print(json.dumps(result))
    return result

  except:
    import traceback
    traceback.print_exc()
    return {
      'statusCode': 500,
      'headers': {
        'content-type': 'application/json',
      },
      'body': json.dumps({
        'message': '処理に失敗しました。'
      })
    }