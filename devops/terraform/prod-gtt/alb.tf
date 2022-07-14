# Application Load Balancer
resource "aws_lb" "app" {
  name            = "app-${var.PROJECT}-${var.NAMESPACE}"
  subnets         = var.PUBLIC_SUBNETS
  security_groups = [aws_security_group.app.id, aws_security_group.lb.id]
  internal        = false
  idle_timeout    = 120
}

# Redirect to HTTPS
resource "aws_lb_listener" "http" {
  load_balancer_arn = aws_lb.app.arn
  port              = "80"
  protocol          = "HTTP"

  default_action {
    type = "redirect"

    redirect {
      port        = "443"
      protocol    = "HTTPS"
      status_code = "HTTP_301"
    }
  }
}

# Public Security Group
resource "aws_security_group" "lb" {
  name        = "lb-${var.PROJECT}-${var.NAMESPACE}"
  description = "Allows HTTP/HTTPS traffic"
  vpc_id      = var.VPC_ID

  ingress {
    description = "All"
    from_port   = 0
    to_port     = 0
    protocol    = "-1"
    cidr_blocks = ["0.0.0.0/0"]
  }

  egress {
    from_port   = 0
    to_port     = 0
    protocol    = "-1"
    cidr_blocks = ["0.0.0.0/0"]
  }

  tags = {
    Name        = "lb-${var.PROJECT}-${var.NAMESPACE}"
    Environment = var.ENV
    Project     = var.PROJECT
    Ns          = var.NAMESPACE
    Domain      = var.DOMAIN
  }
}
