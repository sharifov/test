output "project" {
  value       = var.PROJECT
  description = "Project name"
}

output "env" {
  value       = var.ENV
  description = "Project environment"
}

output "ns" {
  value       = var.NAMESPACE
  description = "Project namespace"
}

output "ip_app" {
  value       = aws_instance.app.*.private_ip
  description = "Private IPs"
}

output "ip_shared" {
  value       = aws_instance.shared.private_ip
  description = "Private IPs"
}

output "domain" {
  value       = var.DOMAIN
  description = "Domain"
}

output "domain_ns" {
  value       = aws_route53_zone.public.name_servers
  description = "Domain Name Servers"
}
