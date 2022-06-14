# Import environment infra tf state
data "terraform_remote_state" "env_parent_zone" {
  backend = "s3"
  config = {
    bucket  = "terraform-stage-traveldev"
    key     = "${var.ENV}"
    region  = "${var.REGION}"
    profile = "aws-stage-infra"
  }
}

# Create NS for this project
resource "aws_route53_record" "project_in_parent" {
  allow_overwrite = true
  name            = var.DOMAIN
  ttl             = 172800
  type            = "NS"
  zone_id         = data.terraform_remote_state.env_parent_zone.outputs.route53_public_zone_id

  records = [
    aws_route53_zone.public.name_servers[0],
    aws_route53_zone.public.name_servers[1],
    aws_route53_zone.public.name_servers[2],
    aws_route53_zone.public.name_servers[3],
  ]
}
