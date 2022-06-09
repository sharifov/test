terraform {
  backend "s3" {
    bucket  = "terraform-stage-crm"
    key     = "stage"
    region  = "us-east-1"
    profile = "aws-stage-infra"
  }
}

provider "aws" {
  region  = var.REGION
  profile = "aws-stage-infra"
}
