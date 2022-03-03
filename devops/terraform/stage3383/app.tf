# App instance
resource "aws_instance" "app" {
  count                  = var.ASG_SIZE
  ami                    = var.APP_AMI
  instance_type          = var.APP_INSTANCE_TYPE
  key_name               = var.SSH_KEY
  subnet_id              = var.PUBLIC_SUBNETS[0]
  vpc_security_group_ids = [aws_security_group.app_private.id]

  root_block_device {
    volume_size = 30
    volume_type = "gp3"
  }

  tags = {
    Name        = "app${count.index}-${var.PROJECT}-${var.ENV}"
    Environment = var.ENV
    Project     = var.PROJECT
    Kind        = "app"
    Ns          = var.NAMESPACE
  }
}

# Private Security Group
resource "aws_security_group" "app_private" {
  name        = "private-${var.PROJECT}-${var.ENV}"
  description = "Allows internal communication betwen ALB and TG"
  vpc_id      = var.VPC_ID

  ingress {
    description = "Self VPC Security Group"
    from_port   = 0
    to_port     = 0
    protocol    = "-1"
    self        = true
  }

  ingress {
    description = "SSH Connections"
    from_port   = 22
    to_port     = 22
    protocol    = "tcp"
    cidr_blocks = ["0.0.0.0/0"]
  }

  ingress {
    description = "APP HTTPS Connections"
    from_port   = 8443
    to_port     = 8443
    protocol    = "tcp"
    cidr_blocks = ["0.0.0.0/0"]
  }

  ingress {
    description = "API HTTPS Connections"
    from_port   = 9443
    to_port     = 9443
    protocol    = "tcp"
    cidr_blocks = ["0.0.0.0/0"]
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
    description = "PHPFPM Exporter"
    from_port   = 9280
    to_port     = 9280
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
    Name        = "private-${var.PROJECT}-${var.ENV}"
    Environment = var.ENV
    Project     = var.PROJECT
    Ns          = var.NAMESPACE
  }
}
