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

## Rich Rule 추가 - Family

### 특정 IP 허용

- 다음 명령어로 192.168.10.11 IP만 접근을 허용합니다.
```
firewall-cmd --permanent --zone=public --add-rich-rule='rule family="ipv4" source address="192.168.10.11" accept'
```

---

### 특정 네트워크 대역 허용

- 다음 명령어로 192.168.11.0/24 대역의 접근을 허용합니다.
```
firewall-cmd --permanent --zone=public --add-rich-rule='rule family="ipv4" source address="192.168.11.0/24" accept'
```

---

### 설정 적용

- 다음 명령어로 저장된 설정을 실제 방화벽에 적용합니다.
```
firewall-cmd --reload
```

- `--permanent`는 설정만 저장하므로 반드시 reload를 해야 적용됩니다.

---

### 설정 확인

- 다음 명령어로 public zone의 Rich Rule 적용 상태를 확인합니다.
```
firewall-cmd --info-zone=public
```

![26](/KH_Security/Linux/Firewall/img/26.png)

- 출력 결과를 통해 현재 public zone에서 http, ssh 서비스가 허용된 상태임을 확인할 수 있습니다.
- 80/tcp 포트가 열려 있어 웹 서비스 접근이 가능한 상태입니다.
- echo-request가 차단되어 ping 요청은 차단된 상태입니다.
- Rich Rule을 통해 192.168.10.11 IP와 192.168.11.0/24 대역만 접근이 허용된 상태입니다.

---

## Rich Rule - Port / Source Port

### Port 설정

- Rich Rule에서 port는 목적지 포트를 의미합니다.
- 특정 포트나 포트 범위를 조건으로 접근을 허용하거나 차단할 때 사용합니다.
- port를 지정할 때는 반드시 tcp 또는 udp 프로토콜을 함께 지정해야 합니다.

### 문법
```
port port="포트번호 또는 범위" protocol="tcp 또는 udp"
```

### 예시
```
port port="1521" protocol="tcp"
port port="1520-1600" protocol="tcp"
```

- 1521번 포트 하나만 지정하거나, 1520~1600번처럼 범위로 지정할 수 있습니다.

---

### Source Port 설정

- source-port는 출발지 포트를 기준으로 제어할 때 사용합니다.
- 일반적인 서비스 접근 제어에서는 목적지 포트를 더 많이 사용합니다.
- 출발지 포트 조건까지 필요한 경우 source-port를 사용합니다.

### 문법
```
source-port port="포트번호 또는 범위" protocol="tcp 또는 udp"
```

### 예시
```
source-port port="1520-1600" protocol="tcp"
```

---

### Rich Rule Port 적용 예시

- 다음 명령어는 192.168.0.0/24 대역에서 1521~1523 TCP 포트로 접근하는 트래픽을 허용 및 실제 방화벽 정책에 적용합니다.
```
firewall-cmd --permanent --zone=public --add-rich-rule='rule family="ipv4"
source address="192.168.0.0/24" port port="1521-1523" protocol="tcp" accept'

firewall-cmd --reload
```

- 다음 명령어로 Rich Rule이 적용되었는지 확인합니다.
```
firewall-cmd --list-all
```

![27](/KH_Security/Linux/Firewall/img/27.png)

- rich rules 항목에 다음과 같은 규칙이 추가된 것을 확인할 수 있습니다.
```
rule family="ipv4" source address="192.168.0.0/24" port port="1521-1523" protocol="tcp" accept
```

- 이 설정은 192.168.0.0/24 대역에서 들어오는 1521~1523 TCP 포트 접근을 허용한다는 의미입니다.
- 즉, 특정 IP 대역과 특정 포트 범위를 동시에 조건으로 설정한 것입니다.

---

## Rich Rule - Service

### Service 설정

- Rich Rule에서 service는 등록되거나 생성된 서비스를 지정합니다.
- port와 protocol을 직접 입력하지 않고 서비스 이름으로 간단하게 제어할 수 있습니다.
- 복잡한 프로토콜이나 여러 포트를 사용하는 서비스는 service 방식이 더 편리합니다.

### 문법
```
service name="서비스명"
```

### 예시
```
service name="ssh"
```

- service name="ssh"는 (port protocol="tcp" port="22")와 의미가 같습니다.
- 즉, ssh 서비스는 22/tcp 포트를 의미합니다.

### Oracle Service 예시

- 사용자 정의 service로 생성한 oracle 서비스도 동일하게 사용할 수 있습니다.
```
family="ipv4" service name="oracle"
```

- oracle service는 앞에서 정의한 1521~1523/tcp 포트를 의미합니다.

---

### Rich Rule Service 적용 예시

- 다음 명령어는 192.168.0.0/24 대역에서 oracle 서비스 접근을 설정하고 실제 방화벽에 적용합니다.
```
firewall-cmd --permanent --zone=public --add-rich-rule='rule family="ipv4" source address="192.168.0.0/24" service name="oracle" accept'

firewall-cmd --reload
```

- 다음 명령어로 Rich Rule 적용 상태를 확인합니다.
```
firewall-cmd --list-all
```

![28](/KH_Security/Linux/Firewall/img/28.png)

- rich rules 항목에 다음과 같은 규칙이 추가된 것을 확인할 수 있습니다.

- 이 설정은 192.168.0.0/24 대역에서 oracle 서비스 접근을 허용한다는 의미입니다.  
  즉, 특정 IP 대역만 oracle 서비스(1521~1523 포트)에 접근할 수 있습니다.

---

## Rich Rule - ICMP Block

### ICMP Block 설정

- ICMP를 종류별로 차단할 때 사용합니다.
- ping 요청처럼 특정 ICMP 타입만 선택적으로 차단할 수 있습니다.
- 전체 차단이 아니라 필요한 ICMP만 제어할 때 사용합니다.

### 문법

- 지정한 ICMP 타입을 차단합니다.
```
icmp-block name="icmp_type"
```

- 다음 명령어로 사용 가능한 ICMP 타입을 확인합니다.
```
firewall-cmd --get-icmptypes
```

### 예시
```
icmp-block name="echo-request"
```

- echo-request는 ping 요청을 의미합니다.
- 즉, ping 요청을 차단하는 설정입니다.

---

### 특정 프로토콜 차단

- 다음 명령어로 IGP 프로토콜을 차단합니다.
```
firewall-cmd --permanent --zone=public --add-rich-rule='rule protocol value="igp" drop'
```

- 지정한 프로토콜의 트래픽을 차단합니다.

---

### Ping 요청 차단

- 다음 명령어로 echo-request(ICMP ping 요청)를 차단합니다.
```
firewall-cmd --permanent --zone=public --add-rich-rule='rule icmp-block name="echo-request"'
```

- 외부에서 ping 요청을 보내도 응답하지 않게 됩니다.

---

### 설정 확인

- 다음 명령어로 실제 방화벽에 적용시킨 후 적용된 내용을 확인합니다.
```
firewall-cmd --reload

firewall-cmd --list-all
```

![29](/KH_Security/Linux/Firewall/img/29.png)

- 출력 결과에서 다음 내용을 확인할 수 있습니다.
```
icmp-blocks: echo-request
rule icmp-block name="echo-request"
rule protocol value="igp" drop
```

- ping 요청은 차단되고, IGP 프로토콜도 차단된 상태입니다.

---
