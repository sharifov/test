# PostgreSQL RDS Instance
module "pgsql" {
  source  = "terraform-aws-modules/rds/aws"
  version = "~> 2.0"

  identifier            = "pgsql-${var.PROJECT}-${var.ENV}"
  engine                = "postgres"
  engine_version        = "12.8"
  major_engine_version  = "12"
  family                = "postgres12"
  instance_class        = var.PGSQL_RDS_INSTANCE_TYPE
  allocated_storage     = var.PGSQL_RDS_VOLUME_SIZE
  max_allocated_storage = var.PGSQL_RDS_VOLUME_MAX
  storage_encrypted     = true
  multi_az              = false
  port                  = "5432"

  name     = var.PGSQL_RDS_DATABASE
  username = var.PGSQL_RDS_USERNAME
  password = var.PGSQL_RDS_PASSWORD

  subnet_ids                          = var.PRIVATE_SUBNETS
  vpc_security_group_ids              = [aws_security_group.pgsql.id]
  create_db_subnet_group              = true
  iam_database_authentication_enabled = false

  maintenance_window = "Mon:00:00-Mon:03:00"
  backup_window      = "03:00-06:00"
  #backup_retention_period = var.IS_PRODUCTION ? 14 : 2
  skip_final_snapshot = true
  deletion_protection = var.IS_PRODUCTION ? true : false

  create_monitoring_role          = true
  monitoring_interval             = "30"
  monitoring_role_name            = "pgsql-${var.PROJECT}-${var.ENV}"
  enabled_cloudwatch_logs_exports = ["postgresql", "upgrade"]
  #performance_insights_enabled          = true
  performance_insights_retention_period = 7

  tags = {
    Project     = var.PROJECT
    Environment = var.ENV
    Ns          = var.NAMESPACE
    Domain      = var.DOMAIN
    Kind        = "db"
    Monitoring  = "prometheus"
    Terraform   = "true"
  }
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

  ingress {
    from_port   = 3306
    to_port     = 3306
    protocol    = "tcp"
    cidr_blocks = [var.INFRA_CIDR]
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
    Terraform   = "true"
  }
}
