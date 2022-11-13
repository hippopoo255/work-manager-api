# フロント用リスナールール
# resource "aws_lb_listener_rule" "front" {
#   listener_arn = aws_lb_listener.https.arn
#   priority     = 100
# 
#   action {
#     type             = "forward"
#     target_group_arn = aws_lb_target_group.front.arn
#   }
# 
#   condition {
#     path_pattern {
#       values = ["/*"]
#     }
#   }
# }

# フロント用ターゲットグループ
# resource "aws_lb_target_group" "front" {
#   name                 = "${var.sg_name_prefix}-tg-front"
#   target_type          = "instance"
#   vpc_id               = var.vpc_id
#   port                 = 3000
#   protocol             = "HTTP"
#   deregistration_delay = 300
# 
#   health_check {
#     path                = "/"
#     healthy_threshold   = 2
#     unhealthy_threshold = 5
#     timeout             = 20
#     interval            = 100
#     matcher             = 200
#     port                = "traffic-port"
#     protocol            = "HTTP"
#   }
# 
#   depends_on = [aws_lb.default]
# }
