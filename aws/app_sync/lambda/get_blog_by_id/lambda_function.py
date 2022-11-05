import boto3
import json
from boto3.dynamodb.conditions import Key, Attr

class InvalidError(Exception):
  def __init__(self, value):
    self.value = value

  def __str__(self):
    return repr(self.value)

dynamodb = boto3.resource('dynamodb')
blogs_table = dynamodb.Table('blogs')

def lambda_handler(event, context):
  print_log('context', json.dumps(event))

  try:
    blog_id = event['arguments']['id']

    print_log('blog_id', json.dumps(blog_id))

    # options = {
    #   # 'Limit': 20,
    #   'FilterExpression': Attr('tags').contains(target_tag_attr),
    #   # 'ScanIndexForward': False,
    # } if target_tag_attr is not None else {
    #   # 'Limit': 20,
    #   # 'ScanIndexForward': False,
    # }

    response = blogs_table.get_item(
      Key = {'id': blog_id},
    )

    print_log('blog_response', json.dumps(response))

    return response['Item'] if "Item" in response else None

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