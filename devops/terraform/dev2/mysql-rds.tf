module "mysql" {
  source  = "terraform-aws-modules/rds/aws"
  version = "~> 2.0"

  identifier        = "mysql-${var.ENV}-${var.PROJECT}"
  engine            = "mysql"
  engine_version    = "8.0.25"
  instance_class    = var.MYSQL_RDS_INSTANCE_TYPE
  allocated_storage = 50
  storage_encrypted = true

  name     = var.MYSQL_RDS_DATABASE
  username = var.MYSQL_RDS_USERNAME
  password = var.MYSQL_RDS_PASSWORD
  port     = "3306"

  vpc_security_group_ids              = [aws_security_group.mysql.id]
  iam_database_authentication_enabled = true

  maintenance_window     = "Mon:00:00-Mon:03:00"
  backup_window          = "03:00-06:00"
  monitoring_interval    = "30"
  monitoring_role_name   = "mysql-${var.ENV}-${var.PROJECT}"
  create_monitoring_role = true

  tags = {
    Project     = var.PROJECT
    Environment = var.ENV
    Ns          = var.NAMESPACE
    Domain      = var.DOMAIN
  }

  subnet_ids           = var.PRIVATE_SUBNETS
  family               = "mysql8.0"
  major_engine_version = "8.0"
  deletion_protection  = false
  skip_final_snapshot  = true

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
  name        = "mysql-${var.PROJECT}-${var.ENV}"
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
    Name        = "mysql-${var.PROJECT}-${var.ENV}"
    Environment = var.ENV
    Project     = var.PROJECT
    Ns          = var.NAMESPACE
    Domain      = var.DOMAIN
  }
}
