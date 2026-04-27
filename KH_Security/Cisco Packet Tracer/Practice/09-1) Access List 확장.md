# ACL (Access Control List) 정리

## 개요

- ACL(Access Control List)은 패킷의 출발지, 목적지, 프로토콜, 포트번호 등을 기준으로 트래픽을 허용하거나 차단하는 기능입니다.
- 라우터에서 특정 통신만 허용하거나 차단할 때 사용합니다.
- 쉽게 말하면 "누가 어디로 어떤 방식으로 접속할 수 있는지"를 제어하는 보안 정책입니다.

---

## 실습 환경

![ACL](/KH_Security/Cisco%20Packet%20Tracer/img/ACL/ACL.png)

---

## ACL 종류

| 종류 | 번호 범위 | 기준 |
|---|---|---|
| 표준 ACL | 1~99, 1300~1999 | 출발지 IP |
| 확장 ACL | 100~199, 2000~2699 | 출발지 + 목적지 + 프로토콜 + 포트 |
| Named 표준 ACL | 이름 사용 | 표준 ACL과 동일 |
| Named 확장 ACL | 이름 사용 | 확장 ACL과 동일 |

- 표준 ACL은 단순하게 출발지 기준만 판단합니다.
- 확장 ACL은 훨씬 세밀하게 제어할 수 있습니다.

---

## ACL 규칙

### Top Down

- 위에서 아래 순서대로 검사합니다.

### First Matching

- 처음 일치한 Rule만 적용됩니다.

### Default Deny

- 마지막에 permit이 없으면 자동으로 deny 됩니다.

---

## 표준 ACL 기본 문법
```
access-list 번호 [permit | deny | remark] 출발지IP wildcard-mask
```

| 항목 | 의미 |
|---|---|
| access-list | ACL 생성 명령어 |
| 번호 | ACL 번호 |
| permit | 허용 |
| deny | 차단 |
| remark | ACL 설명(주석) |
| src_add | 출발지 IP 주소 |
| wildcard mask | 허용하거나 차단할 주소 범위 |

---

## ACL 번호 범위

| 종류 | 번호 범위 |
|---|---|
| 표준 ACL | 1~99 |
| 확장 표준 범위 | 1300~1999 |

---

## Wildcard Mask

- Wildcard Mask는 IP 주소의 범위를 지정할 때 사용합니다.
- Subnet Mask와 반대 개념으로 생각하면 이해가 쉽습니다.

### 주요 Wildcard 값

| Wildcard Mask | 의미 |
|---|---|
| 0.0.0.0 | host (한 대만 지정) |
| 0.0.0.255 | C Class 범위 |
| 0.0.1.255 | 2개 C Class 범위 |
| 255.255.255.255 | any (모든 네트워크) |

---

## ACL 설정 순서

### access-list 생성

- 먼저 ACL 규칙을 생성합니다.

- 예시
```
access-list 1 deny host 2.2.2.3
access-list 1 permit any
```

- 2.2.2.3은 차단하며, 나머지는 모두 허용한다는 뜻입니다.

### 2. 인터페이스 적용

- 생성한 ACL을 실제 인터페이스에 적용합니다.
```
interface [인터페이스]
ip access-group 번호 [in | out]
```

### 구성 요소 설명

| 항목 | 의미 |
|---|---|
| interface | ACL을 적용할 인터페이스 |
| ip access-group | ACL 적용 명령어 |
| 번호 | 적용할 ACL 번호 |
| in | 들어오는 방향 (Inbound) |
| out | 나가는 방향 (Outbound) |

---

## 표준 ACL 설정

- R1 라우팅 설정
```
access-list 1 deny 2.2.2.3 0.0.0.0 [access-list 1 deny host 2.2.2.3]
access-list 1 permit 2.2.0.0 0.0.1.255 // 네트워크 설정
access-list 1 permit any
int s0/0/0
ip access-group 1 in
```

### 결과 확인

- 다음 명령어로 표준 ACL을 확인합니다.
```
show run

show ip access-lists 1
```

![03](/KH_Security/Cisco%20Packet%20Tracer/img/ACL/01.png)

- Serial0/0/0 인터페이스 inbound 방향에 ACL 1번을 적용하여 출발지 IP가 2.2.2.3인 패킷만 차단했습니다.
- 그 결과 2.2.2.3 PC는 Web 접속과 Ping이 실패하고, 나머지 PC는 정상적으로 통신되었습니다

![03](/KH_Security/Cisco%20Packet%20Tracer/img/ACL/02.png)

- ACL 1번에서 `deny host 2.2.2.3`가 먼저 적용되어 해당 PC의 트래픽이 차단되고,  
  이후 `permit any`로 나머지 모든 트래픽은 허용됩니다.
- 따라서 2.2.2.3만 통신이 제한되고 다른 PC들은 정상적으로 접근할 수 있습니다.

---

## ACL 적용

### 처리 순서

1. Inbound ACL  
2. Routing Table 확인  
3. Outbound ACL

- 패킷이 들어오면 먼저 Inbound ACL에서 검사합니다.
- 이후 Routing Table을 확인하여 목적지를 결정합니다.
- 마지막으로 Outbound ACL에서 다시 검사 후 패킷을 전송합니다.

### 중요한 규칙

- ACL 범위가 겹치는 경우 반드시 범위가 좁은 것을 먼저 작성해야 합니다.

#### 예시

- 잘못된 예시
- permit any가 먼저 매칭되어 deny가 동작하지 않습니다.
```
permit any
deny host 2.2.2.3
```

- 올바른 예시
- 특정 Host를 먼저 차단하고 나머지를 허용해야 정상 동작합니다.
```
deny host 2.2.2.3
permit any
```

---

## 확장 ACL 실습 

- 확장 ACL(Extended ACL)은 출발지뿐만 아니라 목적지, 프로토콜, 포트번호까지 확인하여 패킷을 제어합니다.
- 표준 ACL보다 훨씬 세밀한 접근 제어가 가능합니다.
- 라우터의 Static, RIP, OSPF, EIGRP의 R1에다가 확장 ACL을 설정합니다.

### 기본 문법

access-list 번호 [permit | deny | remark] protocol src_address dst_address [sub_protocol]

---

### 구성 요소 설명

| 항목 | 의미 |
|---|---|
| 번호 | ACL 번호 |
| permit | 허용 |
| deny | 차단 |
| remark | ACL 설명 |
| protocol | 사용할 프로토콜 |
| src_address | 출발지 주소 |
| dst_address | 목적지 주소 |
| sub_protocol | 서비스 포트 지정 |

### ACL 번호 범위

| 종류 | 번호 범위 |
|---|---|
| 확장 ACL | 100~199 |
| 확장 확장 범위 | 2000~2699 |

- `##` : # of ACL, 100~199, 2000~2699

- `protocol` : tcp, udp, ip, icmp, ospf, eigrp, ahp, esp, gre

- src, dst_address
  - any : all add
  - host #.#.#.# : host 주소 지정
  - #.#.#.# wildcard_mask : 네트워크 주소 지정

- `sub_protocol` : 서브 프로토콜
  - echo, eq 80(eq www),eq 53(eq domain)등 지정 가

---

## Static 확장 ACL 설정
```
access-list 100 deny tcp host 2.2.1.3 host 1.1.1.11 eq 80
access-list 100 permit ip 2.2.0.0 0.0.0.255 1.1.1.0 0.0.0.255
access-list 100 permit ip 2.2.1.0 0.0.0.255 1.1.1.0 0.0.0.255
access-list 100 permit tcp 2.2.2.0 0.0.0.255 host 1.1.1.11 eq 80

interface s0/0/0
ip access-group 100 in

end
copy running-config startup-config
```

- 다음 명령어로 ACL 설정을 확인합니다.
```
show ip access-lists 100
```

![03](/KH_Security/Cisco%20Packet%20Tracer/img/ACL/03.png)

---

## RIP 확장 ACL 설정
```
access-list 100 permit udp any any eq 520
access-list 100 deny tcp host 2.2.1.3 host 1.1.1.11 eq 80
access-list 100 permit ip 2.2.0.0 0.0.0.255 1.1.1.0 0.0.0.255
access-list 100 permit ip 2.2.1.0 0.0.0.255 1.1.1.0 0.0.0.255
access-list 100 permit tcp 2.2.2.0 0.0.0.255 host 1.1.1.11 eq 80

interface s0/0/0
ip access-group 100 in

end
copy running-config startup-config
```

- 다음 명령어로 ACL 설정을 확인합니다.
```
show ip access-lists 100
```

![04](/KH_Security/Cisco%20Packet%20Tracer/img/ACL/04.png)

---

## OSPF 확장 ACL 설정
```
access-list 100 permit ospf any any
access-list 100 deny tcp host 2.2.1.3 host 1.1.1.11 eq 80
access-list 100 permit ip 2.2.0.0 0.0.0.255 1.1.1.0 0.0.0.255
access-list 100 permit ip 2.2.1.0 0.0.0.255 1.1.1.0 0.0.0.255
access-list 100 permit tcp 2.2.2.0 0.0.0.255 host 1.1.1.11 eq 80

interface s0/0/0
ip access-group 100 in

end
copy running-config startup-config
```

- 다음 명령어로 ACL 설정을 확인합니다.
```
show ip access-lists 100
```

![05](/KH_Security/Cisco%20Packet%20Tracer/img/ACL/05.png)

---

## OSPF 확장 ACL 설정
```
access-list 100 permit ospf any any
access-list 100 deny tcp host 2.2.1.3 host 1.1.1.11 eq 80
access-list 100 permit ip 2.2.0.0 0.0.0.255 1.1.1.0 0.0.0.255
access-list 100 permit ip 2.2.1.0 0.0.0.255 1.1.1.0 0.0.0.255
access-list 100 permit tcp 2.2.2.0 0.0.0.255 host 1.1.1.11 eq 80

interface s0/0/0
ip access-group 100 in

end
copy running-config startup-config
```

- 다음 명령어로 ACL 설정을 확인합니다.
```
show ip access-lists 100
```

![06](/KH_Security/Cisco%20Packet%20Tracer/img/ACL/06.png)

---

## 결과 확인

### PC 1(2.2.1.3/24)

![07](/KH_Security/Cisco%20Packet%20Tracer/img/ACL/07.png)

![08](/KH_Security/Cisco%20Packet%20Tracer/img/ACL/08.png)

- 2.2.1.3 PC는 Ping은 정상적으로 되지만 Web 서버(1.1.1.11) 접속은 차단되었습니다.
- 이는 `deny tcp host 2.2.1.3 host 1.1.1.11 eq www` Rule로 HTTP(80번 포트)만 차단했기 때문입니다.

---

### PC 1(2.2.1.3/24)

![09](/KH_Security/Cisco%20Packet%20Tracer/img/ACL/09.png)

![10](/KH_Security/Cisco%20Packet%20Tracer/img/ACL/10.png)

- 2.2.2.3 PC는 Ping은 실패하지만 Web 서버 접속은 정상적으로 가능합니다.
- 이는 `permit tcp 2.2.2.0 0.0.0.255 host 1.1.1.11 eq www` Rule로 웹 서비스만 허용했기 때문입니다.

---

## 요약 정리

- 표준 ACL은 출발지 IP만 기준으로 패킷을 허용하거나 차단하며, 특정 Host 차단에 주로 사용합니다.
- 확장 ACL은 출발지, 목적지, 프로토콜, 포트까지 확인하여 더 세밀한 접근 제어가 가능합니다.

- ACL은 위에서 아래 순서로 검사하며, 처음 일치한 Rule만 적용됩니다.  
  따라서 범위가 좁은 Rule을 먼저 작성하고, 마지막에는 `permit any`를 넣어야 전체 차단을 방지할 수 있습니다.

- 이번 실습에서는 2.2.2.3은 Ping 차단 + Web 허용,  
  2.2.1.3은 Ping 허용 + Web 차단으로 설정하여 원하는 서비스만 선택적으로 제어되는 것을 확인했습니다.
