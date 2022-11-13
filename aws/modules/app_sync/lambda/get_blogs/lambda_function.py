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
tags_table = dynamodb.Table('tags')

def lambda_handler(event, context):
  print_log('context', json.dumps(event))
  print_log('context2', context)

  #null
  # or
  # { "M": { "id": {"S": "00001"}, "name": {"S": "HTML"} }}
  try:
    args = event['arguments']
    target_tag_attr = tag_attr_by_id(args)

    print_log('target_tag_attr', json.dumps(target_tag_attr))

    options = {
      # 'Limit': 20,
      'FilterExpression': Attr('tags').contains(target_tag_attr),
      # 'ScanIndexForward': False,
    } if target_tag_attr is not None else {
      # 'Limit': 20,
      # 'ScanIndexForward': False,
    }
    response = blogs_table.scan(**options)

    print_log('blog_scan_response', json.dumps(response))

    limit = 20
    page = 2
    start = (page - 1) * limit
    start = 0 if len(response.get('Items', [])) <= start else start
    end = limit * page
    return {
      'items': response.get('Items', [])[start:end],
      'nextToken': args['nextToken'],
    }

  except InvalidError as e:
      print('Invalid Error!!')
      show_err_log(e)

  except Exception as e:
    print('Server Error!!')
    show_err_log(e)

def tag_attr_by_id(arguments):
  tagId = arguments['query']['tag']
  response = tags_table.get_item(
    Key = {'id': tagId},
  )

  return {
    'id': response['Item']['id'],
    'name': response['Item']['name']
  } if "Item" in response else None
  # ↓ Request Mapping Template
  #set( $tagId = $ctx.args.query.tag )
  # {
  #     "operation": "GetItem",
  #     "key": {
  #         "id": $util.dynamodb.toDynamoDBJson($tagId),
  #     }
  # }

def show_err_log(e):
  print('============')
  print(e)
  print('============')

def print_log(title, body):
  print('================')
  print('%s' % (title))
  print(body)
  print('================')


# ↓ Response Mapping Template
# ## Raise a GraphQL field error in case of a datasource invocation error
# #if($ctx.error)
#   $util.error($ctx.error.message, $ctx.error.type)
# #end
# #**
#     Scan and Query operations return a list of items and a nextToken. Pass them
#     to the client for use in pagination.
# *#
# {
#     "items": $util.toJson($context.result.items),
#     ## "items": $util.toJson($ctx.result),
#     "nextToken": $util.toJson($util.defaultIfNullOrBlank($context.result.nextToken, null))
# }