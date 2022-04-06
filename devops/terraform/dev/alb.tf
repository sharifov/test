# Application Load Balancer
resource "aws_lb" "app" {
  name            = "app-${var.PROJECT}-${var.ENV}"
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

# HTTPS Listener
resource "aws_lb_listener" "app" {
  load_balancer_arn = aws_lb.app.arn
  port              = 443
  protocol          = "HTTPS"
  ssl_policy        = "ELBSecurityPolicy-2016-08"
  certificate_arn   = aws_acm_certificate.wildcard_cert.arn

  default_action {
    target_group_arn = aws_lb_target_group.app.arn
    type             = "forward"
  }
}

# App
resource "aws_lb_listener_rule" "app" {
  listener_arn = aws_lb_listener.app.arn
  priority     = 100

  action {
    type             = "forward"
    target_group_arn = aws_lb_target_group.app.arn
  }

  condition {
    host_header {
      values = ["${var.DOMAIN}"]
    }
  }
}

# API
resource "aws_lb_listener_rule" "api" {
  listener_arn = aws_lb_listener.app.arn
  priority     = 99

  action {
    type             = "forward"
    target_group_arn = aws_lb_target_group.api.arn
  }

  condition {
    host_header {
      values = ["api.${var.DOMAIN}"]
    }
  }
}

# WebSocket
resource "aws_lb_listener_rule" "ws" {
  listener_arn = aws_lb_listener.app.arn
  priority     = 98

  action {
    type             = "forward"
    target_group_arn = aws_lb_target_group.ws.arn
  }

  condition {
    host_header {
      values = ["ws.${var.DOMAIN}"]
    }
  }
}

# Centrifugo
resource "aws_lb_listener_rule" "centrifugo" {
  listener_arn = aws_lb_listener.app.arn
  priority     = 97

  action {
    type             = "forward"
    target_group_arn = aws_lb_target_group.centrifugo.arn
  }

  condition {
    host_header {
      values = ["centrifugo.${var.DOMAIN}"]
    }
  }
}

# Public Security Group
resource "aws_security_group" "lb" {
  name        = "lb-${var.PROJECT}-${var.ENV}"
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
    Name        = "lb-${var.PROJECT}-${var.ENV}"
    Environment = var.ENV
    Project     = var.PROJECT
    Ns          = var.NAMESPACE
  }
}
