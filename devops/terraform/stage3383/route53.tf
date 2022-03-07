# Public hosted zone
resource "aws_route53_zone" "public" {
  name = var.DOMAIN
  tags = {
    Environment = var.ENV
    Terraform   = "true"
  }
}

# app host records
resource "aws_route53_record" "app_public" {
  count   = length(aws_instance.app)
  zone_id = aws_route53_zone.public.zone_id
  name    = "app${count.index}"
  type    = "A"
  ttl     = "300"
  records = [aws_instance.app[count.index].public_ip]
}

# shared host record
resource "aws_route53_record" "shared_public" {
  zone_id = aws_route53_zone.public.zone_id
  name    = "shared"
  type    = "A"
  ttl     = "300"
  records = [aws_instance.shared.public_ip]
}

# mysql host record
resource "aws_route53_record" "mysql_public" {
  zone_id = aws_route53_zone.public.zone_id
  name    = "mysql"
  type    = "CNAME"
  ttl     = "300"
  records = ["mysql-stage3383-hybrid.ckacq1ya9k7n.us-east-1.rds.amazonaws.com."]
}

# pgsql host record
resource "aws_route53_record" "pgsql_public" {
  zone_id = aws_route53_zone.public.zone_id
  name    = "pgsql"
  type    = "CNAME"
  ttl     = "300"
  records = ["pgsql-stage3383-hybrid.ckacq1ya9k7n.us-east-1.rds.amazonaws.com."]
}

# app host record
resource "aws_route53_record" "www_public" {
  zone_id = aws_route53_zone.public.zone_id
  name    = var.DOMAIN
  type    = "A"
  ttl     = "300"
  records = ["54.145.217.38", "54.159.207.78"]

}

# api
resource "aws_route53_record" "api_public" {
  zone_id = aws_route53_zone.public.zone_id
  name    = "api"
  type    = "CNAME"
  ttl     = "300"
  records = ["app-hybrid-stage3383-1796368896.us-east-1.elb.amazonaws.com."]
}


# Private hosted zone
resource "aws_route53_zone" "private" {
  name = var.DOMAIN
  vpc {
    vpc_id = var.VPC_ID
  }
  tags = {
    Environment = var.ENV
    Terraform   = "true"
  }
}

# app host records
resource "aws_route53_record" "app_private" {
  count   = length(aws_instance.app)
  zone_id = aws_route53_zone.private.zone_id
  name    = "app${count.index}"
  type    = "A"
  ttl     = "300"
  records = [aws_instance.app[count.index].private_ip]
}

# shared host record
resource "aws_route53_record" "shared_private" {
  zone_id = aws_route53_zone.private.zone_id
  name    = "shared"
  type    = "A"
  ttl     = "300"
  records = [aws_instance.shared.private_ip]
}

# mysql host record
resource "aws_route53_record" "mysql_private" {
  zone_id = aws_route53_zone.private.zone_id
  name    = "mysql"
  type    = "CNAME"
  ttl     = "300"
  records = ["mysql-stage3383-hybrid.ckacq1ya9k7n.us-east-1.rds.amazonaws.com."]
}

# pgsql host record
resource "aws_route53_record" "pgsql_private" {
  zone_id = aws_route53_zone.private.zone_id
  name    = "pgsql"
  type    = "CNAME"
  ttl     = "300"
  records = ["pgsql-stage3383-hybrid.ckacq1ya9k7n.us-east-1.rds.amazonaws.com."]
}

# app host record
resource "aws_route53_record" "www_private" {
  zone_id = aws_route53_zone.private.zone_id
  name    = var.DOMAIN
  type    = "A"
  ttl     = "300"
  records = ["54.145.217.38", "54.159.207.78"]

}

# api
resource "aws_route53_record" "api_private" {
  zone_id = aws_route53_zone.private.zone_id
  name    = "api"
  type    = "CNAME"
  ttl     = "300"
  records = ["app-hybrid-stage3383-1796368896.us-east-1.elb.amazonaws.com."]
}
