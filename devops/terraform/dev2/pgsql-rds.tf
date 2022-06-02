# PostgreSQL RDS Instance
module "pgsql" {
  source     = "terraform-aws-modules/rds/aws"
  version    = "~> 2.0"
  identifier = "pgsql-${var.PROJECT}-${var.ENV}"

  engine            = "postgres"
  engine_version    = "12.8"
  instance_class    = var.PGSQL_RDS_INSTANCE_TYPE
  allocated_storage = 30
  storage_encrypted = true
  multi_az          = false

  name     = var.PGSQL_RDS_DATABASE
  username = var.PGSQL_RDS_USERNAME
  password = var.PGSQL_RDS_PASSWORD
  port     = 5432

  vpc_security_group_ids  = [aws_security_group.pgsql.id]
  maintenance_window      = "Mon:00:00-Mon:03:00"
  backup_window           = "03:00-06:00"
  backup_retention_period = 0
  apply_immediately       = true

  tags = {
    Terraform   = "true"
    Environment = var.ENV
    Project     = var.PROJECT
    Ns          = var.NAMESPACE
    Domain      = var.DOMAIN
  }

  enabled_cloudwatch_logs_exports = ["postgresql", "upgrade"]
  subnet_ids                      = var.PRIVATE_SUBNETS
  family                          = "postgres12"
  major_engine_version            = "12"
  final_snapshot_identifier       = "pgsql-${var.PROJECT}-${var.ENV}"
  deletion_protection             = false
  skip_final_snapshot             = true
}


# PostgreSQL SecurityGroup
resource "aws_security_group" "pgsql" {
  name        = "pgsql-${var.PROJECT}-${var.ENV}"
  vpc_id      = var.VPC_ID
  description = "Allows PostgreSQL connections within ${var.ENV} VPC"

  lifecycle {
    create_before_destroy = true
  }

  ingress {
    from_port   = 5432
    to_port     = 5432
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
    Name        = "mysql-${var.PROJECT}-${var.ENV}"
    Environment = var.ENV
    Project     = var.PROJECT
    Ns          = var.NAMESPACE
    Domain      = var.DOMAIN
  }
}
