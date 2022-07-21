# IAM User
resource "aws_iam_user" "attachments" {
  name = "attachments-${var.PROJECT}-${var.NAMESPACE}"
  tags = {
    S3 = "attachments-${var.PROJECT}-${var.NAMESPACE}"
  }
}

# IAM User Policy
resource "aws_iam_user_policy" "s3_attachments_write" {
  name   = "attachments-${var.PROJECT}-${var.NAMESPACE}-write-policy"
  user   = aws_iam_user.attachments.name
  policy = data.aws_iam_policy_document.s3_attachments_write_permissions.json
}

data "aws_iam_policy_document" "s3_attachments_write_permissions" {
  statement {
    effect = "Allow"

    actions = [
      "s3:Get*",
      "s3:List*",
      "s3:AbortMultipartUpload",
      "s3:ListMultipartUploadParts",
      "s3:PutObject",
      "s3:DeleteObject"
    ]

    resources = [
      "arn:aws:s3:::attachments-${var.PROJECT}-${var.NAMESPACE}",
      "arn:aws:s3:::attachments-${var.PROJECT}-${var.NAMESPACE}/*"
    ]
  }
}
