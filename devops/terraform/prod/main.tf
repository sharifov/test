terraform {
  backend "s3" {
    bucket  = "terraform-prod-crm-v1"
    key     = "prod"
    region  = "us-east-1"
    profile = "aws-prod-infra"
  }
}

provider "aws" {
  region  = var.REGION
  profile = "aws-prod-infra"
}
