# Beanstalkd, Redis, Centrifugo EC2 Instance
resource "aws_instance" "shared" {
  ami                    = var.SHARED_AMI
  instance_type          = var.SHARED_INSTANCE_TYPE
  key_name               = var.SSH_KEY
  subnet_id              = var.PRIVATE_SUBNETS[0]
  vpc_security_group_ids = [aws_security_group.shared.id]

  root_block_device {
    volume_size = 30
    volume_type = "gp3"
  }

  tags = {
    Name        = "shared-${var.PROJECT}-${var.ENV}"
    Environment = var.ENV
    Project     = var.PROJECT
    Ns          = var.NAMESPACE
    Kind        = "shared"
  }
}

# SecurityGroup
resource "aws_security_group" "shared" {
  name        = "shared-${var.PROJECT}-${var.ENV}"
  description = "Allows Beanstalk & Redis within ${var.ENV} VPC"
  vpc_id      = var.VPC_ID

  ingress {
    description = "SSH"
    from_port   = 22
    to_port     = 22
    protocol    = "tcp"
    cidr_blocks = ["0.0.0.0/0"]
  }

  ingress {
    description = "Beanstalkd"
    from_port   = 11300
    to_port     = 11300
    protocol    = "tcp"
    cidr_blocks = [var.VPC_CIDR]
  }

  ingress {
    description = "Redis"
    from_port   = 6379
    to_port     = 6379
    protocol    = "tcp"
    cidr_blocks = [var.VPC_CIDR]
  }

  ingress {
    description = "Centrifugo"
    from_port   = 8000
    to_port     = 8000
    protocol    = "tcp"
    cidr_blocks = [var.VPC_CIDR]
  }

  ingress {
    description = "Filebeat"
    from_port   = 5066
    to_port     = 5066
    protocol    = "tcp"
    cidr_blocks = [var.VPC_CIDR]
  }

  ingress {
    description = "Node Exporter"
    from_port   = 9100
    to_port     = 9100
    protocol    = "tcp"
    cidr_blocks = [var.VPC_CIDR]
  }

  ingress {
    description = "Beats Exporter"
    from_port   = 9479
    to_port     = 9479
    protocol    = "tcp"
    cidr_blocks = [var.VPC_CIDR]
  }

  ingress {
    description = "Beanstalkd Exporter"
    from_port   = 9300
    to_port     = 9300
    protocol    = "tcp"
    cidr_blocks = [var.VPC_CIDR]
  }

  ingress {
    description = "Redis Exporter"
    from_port   = 9121
    to_port     = 9121
    protocol    = "tcp"
    cidr_blocks = [var.VPC_CIDR]
  }

  egress {
    from_port   = 0
    to_port     = 0
    protocol    = "-1"
    cidr_blocks = ["0.0.0.0/0"]
  }

  tags = {
    Name        = "shared-${var.PROJECT}-${var.ENV}"
    Environment = var.ENV
    Project     = var.PROJECT
    Ns          = var.NAMESPACE
  }
}
