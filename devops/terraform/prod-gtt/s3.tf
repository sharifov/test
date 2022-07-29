# S3 bucket for static content
resource "aws_s3_bucket" "attachments" {
  bucket = "attachments-${var.PROJECT}-${var.NAMESPACE}"
  tags = {
    Name        = "attachments-${var.PROJECT}-${var.NAMESPACE}"
    Environment = var.ENV
    Project     = var.PROJECT
    Domain      = var.DOMAIN
    Ns          = var.NAMESPACE
    Terraform   = true
  }
}
