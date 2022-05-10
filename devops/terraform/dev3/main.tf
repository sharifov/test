terraform {
  backend "s3" {
    bucket  = "terraform-dev3-crm"
    key     = "dev"
    region  = "us-east-1"
    profile = "aws-dev-infra"
  }
}

provider "aws" {
  region  = var.REGION
  profile = "aws-dev-infra"
}
