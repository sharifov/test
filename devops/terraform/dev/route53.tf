# Public hosted zone
resource "aws_route53_zone" "public" {
  name = var.DOMAIN
  tags = {
    Environment = var.ENV
    Terraform   = "true"
  }
}

# app host records
resource "aws_route53_record" "app" {
  count   = length(aws_instance.app)
  zone_id = aws_route53_zone.public.zone_id
  name    = "app${count.index}"
  type    = "A"
  ttl     = "300"
  records = [aws_instance.app[count.index].private_ip]
}

# shared host record
resource "aws_route53_record" "shared" {
  zone_id = aws_route53_zone.public.zone_id
  name    = "shared"
  type    = "A"
  ttl     = "300"
  records = [aws_instance.shared.private_ip]
}

# mysql host record
resource "aws_route53_record" "mysql" {
  zone_id = aws_route53_zone.public.zone_id
  name    = "mysql"
  type    = "CNAME"
  ttl     = "300"
  records = [module.mysql.this_db_instance_address]
}

# pgsql host record
resource "aws_route53_record" "pgsql" {
  zone_id = aws_route53_zone.public.zone_id
  name    = "pgsql"
  type    = "CNAME"
  ttl     = "300"
  records = [module.pgsql.this_db_instance_address]
}

