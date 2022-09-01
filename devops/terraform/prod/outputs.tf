output "_project" {
  value       = var.PROJECT
  description = "Project name"
}

output "_env" {
  value       = var.ENV
  description = "Project environment"
}

output "_ns" {
  value       = var.NAMESPACE
  description = "Project namespace"
}

output "_domain" {
  value       = var.DOMAIN
  description = "Domain"
}

output "zone_name_servers" {
  value       = aws_route53_zone.public.name_servers
  description = "Domain Name Servers"
}

output "ec2_app_ip" {
  value       = aws_instance.app.*.private_ip[0]
  description = "app srv ip"
}

output "ec2_shared_ip" {
  value       = aws_instance.shared.private_ip
  description = "shared srv ip"
}

