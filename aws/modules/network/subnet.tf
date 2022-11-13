resource "aws_subnet" "public" {
  count                   = length(local.public_subnets)
  vpc_id                  = aws_vpc.this.id
  cidr_block              = values(local.public_subnets)[count.index].cidr_block
  map_public_ip_on_launch = true
  availability_zone       = values(local.public_subnets)[count.index].availability_zone
  tags = {
    Name = values(local.public_subnets)[count.index].name
  }
}

resource "aws_subnet" "private" {
  count                   = length(local.private_subnets)
  vpc_id                  = aws_vpc.this.id
  cidr_block              = values(local.private_subnets)[count.index].cidr_block
  map_public_ip_on_launch = false
  availability_zone       = values(local.private_subnets)[count.index].availability_zone
  tags = {
    Name = values(local.private_subnets)[count.index].name
  }
}