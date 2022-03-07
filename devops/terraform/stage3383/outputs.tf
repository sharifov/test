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
  value       = aws_instance.app.*.public_ip
  description = "Public IPs"
}

output "ip_shared" {
  value       = aws_instance.shared.public_ip
  description = "Public IPs"
}
