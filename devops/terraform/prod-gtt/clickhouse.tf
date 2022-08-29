# ClickHouse EC2 Instnace
resource "aws_instance" "clickhouse" {
  ami                    = var.CLICKHOUSE_AMI
  instance_type          = var.CLICKHOUSE_INSTANCE_TYPE
  key_name               = var.SSH_KEY
  subnet_id              = var.PRIVATE_SUBNETS[0]
  vpc_security_group_ids = [aws_security_group.clickhouse.id]

  root_block_device {
    volume_size = var.CLICKHOUSE_VOLUME_SIZE
    volume_type = "gp3"
    encrypted   = true
  }

  tags = {
    Name        = "clickhouse-${var.PROJECT}-${var.NAMESPACE}"
    Environment = var.ENV
    Project     = var.PROJECT
    Ns          = var.NAMESPACE
    Domain      = var.DOMAIN
    App         = "clickhouse"
    Kind        = "clickhouse"
    Monitoring  = "prometheus"
    Terraform   = "true"
  }
}

# Security Group
resource "aws_security_group" "clickhouse" {
  name        = "clickhouse-${var.PROJECT}-${var.NAMESPACE}"
  description = "Allows ClickHouse within ${var.ENV} VPC"
  vpc_id      = var.VPC_ID

  ingress {
    description = "Self VPC Security Group"
    from_port   = 0
    to_port     = 0
    protocol    = "-1"
    self        = true
  }

  ingress {
    description = "SSH"
    from_port   = 22
    to_port     = 22
    protocol    = "tcp"
    cidr_blocks = ["0.0.0.0/0"]
  }

  ingress {
    description = "ClickHouse http"
    from_port   = 8123
    to_port     = 8123
    protocol    = "tcp"
    cidr_blocks = [var.VPC_CIDR, var.INFRA_CIDR]
  }

  ingress {
    description = "ClickHouse binary"
    from_port   = 9000
    to_port     = 9009
    protocol    = "tcp"
    cidr_blocks = [var.VPC_CIDR, var.INFRA_CIDR]
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
    cidr_blocks = [var.VPC_CIDR, var.INFRA_CIDR]
  }

  ingress {
    description = "Beats Exporter"
    from_port   = 9479
    to_port     = 9479
    protocol    = "tcp"
    cidr_blocks = [var.VPC_CIDR, var.INFRA_CIDR]
  }

  egress {
    from_port   = 0
    to_port     = 0
    protocol    = "-1"
    cidr_blocks = ["0.0.0.0/0"]
  }

  tags = {
    Name        = "clickhouse-${var.PROJECT}-${var.NAMESPACE}"
    Environment = var.ENV
    Project     = var.PROJECT
    Ns          = var.NAMESPACE
    Domain      = var.DOMAIN
    Terraform   = "true"
  }
}
