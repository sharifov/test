# Private hosted zone
resource "aws_route53_zone" "private" {
  name = var.DOMAIN
  vpc {
    vpc_id = var.VPC_ID
  }

  tags = {
    Project     = var.PROJECT
    Environment = var.ENV
    Ns          = var.NAMESPACE
    Domain      = var.DOMAIN
    Terraform   = "true"
  }
}

# App
resource "aws_route53_record" "private_app" {
  zone_id = aws_route53_zone.private.zone_id
  name    = var.DOMAIN
  type    = "A"
  alias {
    name                   = aws_lb.app.dns_name
    zone_id                = aws_lb.app.zone_id
    evaluate_target_health = true
  }
}

# API
resource "aws_route53_record" "private_api" {
  zone_id = aws_route53_zone.private.zone_id
  name    = "api"
  type    = "A"
  alias {
    name                   = aws_lb.app.dns_name
    zone_id                = aws_lb.app.zone_id
    evaluate_target_health = true
  }
}

# Websocket Server
resource "aws_route53_record" "private_ws" {
  zone_id = aws_route53_zone.private.zone_id
  name    = "ws"
  type    = "A"
  ttl     = "300"
  records = [aws_instance.shared.private_ip]
}

# Application hosts
resource "aws_route53_record" "private_apps" {
  count   = length(aws_instance.app)
  zone_id = aws_route53_zone.private.zone_id
  name    = "app${count.index}"
  type    = "A"
  ttl     = "300"
  records = [aws_instance.app[count.index].private_ip]
}


# Shared host
resource "aws_route53_record" "private_shared" {
  zone_id = aws_route53_zone.private.zone_id
  name    = "shared"
  type    = "A"
  ttl     = "300"
  records = [aws_instance.shared.private_ip]
}

# MySQL
resource "aws_route53_record" "private_mysql" {
  zone_id = aws_route53_zone.private.zone_id
  name    = "mysql"
  type    = "CNAME"
  ttl     = "300"
  records = ["prod-crm-mysql.cmcnc9sukklm.us-east-1.rds.amazonaws.com."]
}

# PostgreSQL
resource "aws_route53_record" "private_pgsql" {
  zone_id = aws_route53_zone.private.zone_id
  name    = "pgsql"
  type    = "CNAME"
  ttl     = "300"
  records = ["prod-crm-postgres.cmcnc9sukklm.us-east-1.rds.amazonaws.com."]
}

# Redis
resource "aws_route53_record" "private_redis" {
  zone_id = aws_route53_zone.private.zone_id
  name    = "redis"
  type    = "A"
  ttl     = "300"
  records = [aws_instance.shared.private_ip]
}

# Centrifugo
resource "aws_route53_record" "private_centrifugo" {
  zone_id = aws_route53_zone.private.zone_id
  name    = "centrifugo"
  type    = "A"
  ttl     = "300"
  records = [aws_instance.shared.private_ip]
}

# Beanstalkd
resource "aws_route53_record" "private_beanstalkd" {
  zone_id = aws_route53_zone.private.zone_id
  name    = "beanstalkd"
  type    = "A"
  ttl     = "300"
  records = [aws_instance.shared.private_ip]
}

# Call antispam
resource "aws_route53_record" "private_antispam" {
  zone_id = aws_route53_zone.private.zone_id
  name    = "antispam"
  type    = "A"
  ttl     = "300"
  records = [aws_instance.shared.private_ip]
}

# ClickHouse
resource "aws_route53_record" "private_clickhouse" {
  zone_id = aws_route53_zone.private.zone_id
  name    = "clickhouse"
  type    = "A"
  ttl     = "300"
  records = [aws_instance.clickhouse.private_ip]
}
