# haproxy-exporter role

## Description

Deploy prometheus [haproxy exporter](https://github.com/prometheus/haproxy_exporter) using ansible.

## Role Variables

Variables from [defaults/main.yml](defaults/main.yml) can be overridden.

| Name           | Default | Description                        |
| -------------- | ------------- | -----------------------------------|
| `haproxy_exporter_version` | 0.10.0 | Haproxy exporter version |
| `haproxy_exporter_web_listen_address` | "0.0.0.0:9101" | Exporter Listen address |
| `haproxy_scrape_url` | "http://admin:pass@127.0.0.1:2324/haproxy?stats;csv" | Haproxy stats url |

### Playbook

Use haproxy exporter role in a playbook:
```yaml
  roles:
    - role: haproxy-exporter
      become: yes
      vars:
        haproxy_scrape_url: "http://admin:pass@127.0.0.1:2324/haproxy?stats;csv"
```


### Note:

Avoid special chars in auth string.