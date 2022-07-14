module "mysql" {
  source  = "terraform-aws-modules/rds/aws"
  version = "~> 4.4.0"

  identifier            = "mysql-${var.PROJECT}-${var.NAMESPACE}"
  engine                = "mysql"
  engine_version        = "8.0.28"
  instance_class        = var.MYSQL_RDS_INSTANCE_TYPE
  allocated_storage     = var.MYSQL_RDS_VOLUME_SIZE
  max_allocated_storage = var.MYSQL_RDS_VOLUME_LIMIT
  storage_encrypted     = true
  port                  = "3306"

  db_name                = var.MYSQL_RDS_DATABASE
  username               = var.MYSQL_RDS_USERNAME
  password               = var.MYSQL_RDS_PASSWORD
  create_random_password = false

  vpc_security_group_ids              = [aws_security_group.mysql.id]
  iam_database_authentication_enabled = true

  maintenance_window     = "Mon:00:00-Mon:03:00"
  backup_window          = "03:00-06:00"
  monitoring_interval    = "30"
  monitoring_role_name   = "mysql-${var.PROJECT}-${var.NAMESPACE}"
  create_monitoring_role = true

  create_db_subnet_group  = true
  subnet_ids              = var.PRIVATE_SUBNETS
  family                  = "mysql8.0"
  major_engine_version    = "8.0"
  deletion_protection     = var.IS_PRODUCTION ? true : false
  skip_final_snapshot     = true
  backup_retention_period = var.IS_PRODUCTION ? 14 : 2

  enabled_cloudwatch_logs_exports       = ["audit", "error", "general"]
  create_cloudwatch_log_group           = true
  performance_insights_enabled          = true
  performance_insights_retention_period = 7

  parameters = [
    {
      name  = "character_set_client"
      value = "utf8mb4"
    },
    {
      name  = "character_set_server"
      value = "utf8mb4"
    }
  ]

  options = [
    {
      option_name = "MARIADB_AUDIT_PLUGIN"
      option_settings = [
        {
          name  = "SERVER_AUDIT_EVENTS"
          value = "CONNECT"
        },
        {
          name  = "SERVER_AUDIT_FILE_ROTATIONS"
          value = "37"
        },
      ]
    },
  ]
  tags = {
    Project     = var.PROJECT
    Environment = var.ENV
    Ns          = var.NAMESPACE
    Domain      = var.DOMAIN
  }

}

module "mysql_replica" {
  source  = "terraform-aws-modules/rds/aws"
  version = "~> 4.4.0"

  identifier = "mysql-replica-${var.PROJECT}-${var.NAMESPACE}"

  # Source database. For cross-region use db_instance_arn
  replicate_source_db    = module.mysql.db_instance_id
  create_random_password = false

  engine                = "mysql"
  engine_version        = "8.0.28"
  family                = "mysql8.0"
  major_engine_version  = "8.0"
  instance_class        = "db.t4g.small"
  allocated_storage     = var.MYSQL_RDS_VOLUME_SIZE
  max_allocated_storage = var.MYSQL_RDS_VOLUME_LIMIT
  storage_encrypted     = true

  port = "3306"

  multi_az               = false
  vpc_security_group_ids = [aws_security_group.mysql.id]

  maintenance_window = "Tue:00:00-Tue:03:00"
  backup_window      = "03:00-06:00"

  backup_retention_period = 0
  skip_final_snapshot     = true
  deletion_protection     = false

  enabled_cloudwatch_logs_exports       = ["audit", "error", "general"]
  create_cloudwatch_log_group           = true
  performance_insights_enabled          = true
  performance_insights_retention_period = 7

  tags = {
    Environment = var.ENV
    Project     = var.PROJECT
    Ns          = var.NAMESPACE
    Role        = "replica"
  }

  parameters = [
    {
      name  = "character_set_client"
      value = "utf8mb4"
    },
    {
      name  = "character_set_server"
      value = "utf8mb4"
    }
  ]
  options = [
    {
      option_name = "MARIADB_AUDIT_PLUGIN"
      option_settings = [
        {
          name  = "SERVER_AUDIT_EVENTS"
          value = "CONNECT"
        },
        {
          name  = "SERVER_AUDIT_FILE_ROTATIONS"
          value = "37"
        },
      ]
    },
  ]
}

resource "aws_security_group" "mysql" {
  name        = "mysql-${var.PROJECT}-${var.NAMESPACE}"
  vpc_id      = var.VPC_ID
  description = "Allows MySQL connections within ${var.ENV} VPC"

  ingress {
    from_port   = 3306
    to_port     = 3306
    protocol    = "tcp"
    cidr_blocks = [var.VPC_CIDR]
  }

  egress {
    from_port   = 0
    to_port     = 0
    protocol    = "-1"
    cidr_blocks = ["0.0.0.0/0"]
  }

  lifecycle {
    create_before_destroy = true
  }

  tags = {
    Name        = "mysql-${var.PROJECT}-${var.NAMESPACE}"
    Environment = var.ENV
    Project     = var.PROJECT
    Ns          = var.NAMESPACE
    Domain      = var.DOMAIN
  }
}
