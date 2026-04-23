# Rich Rule 설정

## 개요

- Rich Rule은 기본 service, port 설정으로 부족한 경우 사용하는 상세 방화벽 정책입니다.

- 특정 IP만 허용하거나 차단하고 싶을 때 사용합니다.

- 특정 IP만 특정 서비스에 접근하도록 제한할 수 있습니다.

- 즉, “누가 어떤 서비스에 접근할 수 있는지”를 제어하는 기능입니다.

---

## Rich Rule 명령어

- Rich Rule은 추가(add), 삭제(remove), 확인(query) 3가지로 관리합니다.  

| 기능 | 명령어 | 설명 |
|------|--------|------|
| rule 추가 | firewall-cmd [--zone=zone] --add-rich-rule='rule ...' | 조건 기반 방화벽 규칙을 추가 |
| rule 삭제 | firewall-cmd [--zone=zone] --remove-rich-rule='rule ...' | 기존에 설정된 Rich Rule을 제거 |
| rule 확인 | firewall-cmd [--zone=zone] --query-rich-rule='rule ...' | 특정 Rich Rule이 적용되어 있는지 확인 |

- zone은 `default zone`을 기본으로 합니다.

---

## Rich Rule 구조

```
rule
  family="rule family"
  source
  destination
  service|port|protocol|icmp-block|icmp-type|masquerade|forward-port|source-port
  accept|reject|drop|mark]
  log
  audit
  --timeout=timeval
```

| 항목 | 의미 | 항목 | 의미 |
|------|------|------|------|
| rule | 하나의 방화벽 정책 단위를 의미 | source-port | 출발지 포트 기준으로 제어 |
| family | IPv4 또는 IPv6 적용 범위를 지정 | accept | 트래픽을 허용 |
| source | 출발지 IP(누가 접근하는지)를 지정 | drop | 트래픽을 삭제 (응답 없음) |
| destination | 목적지 IP(어디로 가는지)를 지정 | reject | 트래픽을 거부 (응답 있음) |
| service | 서비스 단위로 접근 제어 | mark | 패킷에 표시 |
| port | 특정 포트 접근을 제어 | log | 트래픽 로그를 기록합 |
| protocol | 특정 프로토콜(ICMP 등)을 제어 | audit | 보안 감사 로그를 기록 |
| icmp-block | 특정 ICMP 타입을 차단 | timeout | 일정 시간 동안만 rule을 적용 |

---

## Rich Rule - source / destination

- source는 출발지 IP를 지정합니다.
- destination은 목적지 IP를 지정합니다.
- 특정 IP만 허용하거나 차단할 때 사용합니다.
- 단일 IP와 CIDR 대역 모두 설정 가능합니다.
- `not` 옵션으로 특정 IP 대역을 제외할 수 있습니다.

### 문법
```
source|destination [not] address="address[/mask]"
```

### 예시

- 특정 IP만 허용
```
family=ipv4 source address="192.168.10.11"
```

- 특정 대역 제외
```
family=ipv4 source not address="192.168.10.0/24"
```

- 특정 목적지 지정
```
family=ipv4 destination address="192.168.11.0/24"
```

- 출발지 + 목적지 동시 지정
```
family=ipv4 source address="192.168.10.11" destination address="192.168.11.11"
```

---

### Rich Rule - Family 실습

