# Variables
variable "REGION" {
  default     = "us-east-1"
  description = "AWS Region"
  type        = string
}

variable "VPC_ID" {
  default     = "vpc-018ac02d18d1343b4"
  description = "VPC ID"
  type        = string
}

variable "VPC_CIDR" {
  default     = "10.100.0.0/16"
  description = "VPC Subnet Range"
  type        = string
}

variable "PRIVATE_SUBNETS" {
  default     = ["subnet-0c8befd95c3b93c5f", "subnet-0135ab73c852e318d", "subnet-06ec9701ba511a40a"]
  description = "Private Subnet Range"
  type        = list(string)
}

variable "PUBLIC_SUBNETS" {
  default     = ["subnet-0dfdddfa02f823acb", "subnet-0e961d9019e832bdc", "subnet-013804eaa496babf4"]
  description = "Public Subnet Range"
  type        = list(string)
}

variable "PROJECT" {
  default     = "crm"
  description = "Project name"
  type        = string
}

variable "ENV" {
  default     = "stage3383"
  description = "Environment name"
  type        = string
}

variable "NAMESPACE" {
  default     = "kiv"
  description = "Namespace"
  type        = string
}

variable "DOMAIN" {
  default     = "crm.stage3383.travel-dev.com"
  description = "Domain name"
  type        = string
}

variable "SSH_KEY" {
  type        = string
  description = "SSH Access Key"
  default     = "dev"
}

variable "ASG_SIZE" {
  type        = string
  description = "Autoscaling group size"
  default     = "1"
}

variable "APP_INSTANCE_TYPE" {
  type        = string
  description = "EC2 instance type"
  default     = "t3.medium"
}

variable "APP_AMI" {
  type        = string
  description = "AMI"
  default     = "ami-04505e74c0741db8d"
}

variable "SHARED_INSTANCE_TYPE" {
  type        = string
  description = "EC2 instance type"
  default     = "t3.medium"
}

variable "SHARED_AMI" {
  type        = string
  description = "AMI"
  default     = "ami-04505e74c0741db8d"
}
