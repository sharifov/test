terraform {
  backend "s3" {
    bucket  = "terraform-stage3383-crm"
    key     = "stage3383"
    region  = "us-east-1"
    profile = "aws-stage-infra"
  }
}

provider "aws" {
  region  = var.REGION
  profile = "aws-stage-infra"
}
