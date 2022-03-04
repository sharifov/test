# S3 bucket for static content
resource "aws_s3_bucket" "attachments" {
  bucket = "attachments-${var.PROJECT}-${var.ENV}"
  tags = {
    Name        = "attachments-${var.PROJECT}-${var.ENV}"
    Environment = "${var.ENV}"
    Project     = "crm"
    Terraform   = true
  }
}
