output "vpc_id" {
  value = aws_vpc.this.id
}

output "vpc_cidr_block" {
  value = aws_vpc.this.cidr_block
}

output "subnet_public_ids" {
  value = [for k, v in aws_subnet.public : v.id]

  depends_on = [aws_subnet.public]
}

output "subnet_private_ids" {
  value = [for k, v in aws_subnet.private : v.id]

  depends_on = [aws_subnet.private]
}