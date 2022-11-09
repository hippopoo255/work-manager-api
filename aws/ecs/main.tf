resource "aws_ecs_cluster" "this" {
  name = "TFWorkManagerCluster"
}

resource "aws_ecs_service" "this" {
  name            = "work-manager-service-back"
  cluster         = aws_ecs_cluster.this.name
  task_definition = aws_ecs_task_definition.back.arn
  desired_count   = "1"

  health_check_grace_period_seconds  = 30
  load_balancer {
    target_group_arn = var.target_group_back_arn
    container_name   = "web"
    container_port   = 80
  }

  placement_constraints {
    # インスタンスごとに 1 つのタスクのみを配置します
    type = "distinctInstance" 
  }
}
