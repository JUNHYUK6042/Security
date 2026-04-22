# Rich Rule 설정

## 개요

- Rich Rule은 기본 service/port 설정으로 표현할 수 없는  
  **세밀한 방화벽 정책을 정의하기 위해 사용합니다.** 

- 특정 IP만 허용, 특정 IP만 차단, 특정 조건에서만 포트 허용 등  
  **조건 기반 제어가 필요할 때 사용합니다.**

- 즉, “누가 + 무엇을 + 어떻게” 접근할지까지 제어 가능합니다.

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
| rule | 하나의 방화벽 정책 단위를 의미합니다 | source-port | 출발지 포트 기준으로 제어합니다 |
| family | IPv4 또는 IPv6 적용 범위를 지정합니다 | accept | 트래픽을 허용합니다 |
| source | 출발지 IP(누가 접근하는지)를 지정합니다 | drop | 트래픽을 버립니다 (응답 없음) |
| destination | 목적지 IP(어디로 가는지)를 지정합니다 | reject | 트래픽을 거부합니다 (응답 있음) |
| service | 서비스 단위로 접근 제어를 합니다 | mark | 패킷에 표시를 남깁니다 |
| port | 특정 포트 접근을 제어합니다 | log | 트래픽 로그를 기록합니다 |
| protocol | 특정 프로토콜(ICMP 등)을 제어합니다 | audit | 보안 감사 로그를 기록합니다 |
| icmp-block | 특정 ICMP 타입을 차단합니다 | timeout | 일정 시간 동안만 rule을 적용합니다 |

---


