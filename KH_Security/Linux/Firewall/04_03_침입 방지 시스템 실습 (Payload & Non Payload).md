# 침입 탐지 시스템 실습 (Payload, Non Payload)

## 개요

- Snort를 이용하여 다양한 공격 및 접근 시도를 탐지하는 실습입니다.
- 실제 패킷을 발생시키고 Rule을 직접 작성하여 alert 로그를 확인합니다.
- Payload, Non-Payload, flow, threshold를 이용해 세밀한 탐지 정책을 구성합니다.
- 단순 Ping 탐지부터 Scan, Flooding, Land Attack까지 실제 공격 형태를 실습합니다.

---

## 실습 전 공통 실행

- 모든 탐지 실습을 진행하기 전에 IDS 서버에서 먼저 Snort를 실행합니다.
- 즉, 이후 진행하는 모든 탐지 실습은 모두 이 명령어를 먼저 실행한 뒤 테스트를 진행합니다.
```
snort -i ens160 -A fast -d -c /etc/snort/snort.conf
```

- 모든 Rule 설정도 다음과 같은 명령어로 파일에 적혀있는 Rule을 수정합니다.
```
vi /etc/snort/rules/local.rules
```

---

## Option - payload 

### Rule 설정

- `vi /etc/snort/rules/local.rules` 명령어를 통해
  local.rules 파일에 다음과 같이 Rule을 설정합니다.

![42](/KH_Security/Linux/Firewall/img/42.png)

---

### HTTP GET 탐지

- 다음과 같은 Rule 설정으로 HTTP 요청 중 GET 문자열을 탐지합니다.
```
alert tcp any any -> any 80 (msg:"HTTP GET Detect"; content:"GET"; sid:1000105; rev:1;)
```

- 목적지 80번 포트(HTTP)로 가는 TCP 패킷 중 payload에 GET 문자열이 포함되면 탐지합니다.
- 웹 접속 요청(HTTP GET)을 탐지하는 Rule입니다.

#### 결과 확인

- EXTERNAL 서버(192.168.11.19)에서 Web 서버(192.168.12.11)의 웹 서비스에 접속합니다.
```
curl http://192.168.12.11
```

![43](/KH_Security/Linux/Firewall/img/43.png)

- 로그를 보면 `HTTP GET Detect`가 정상적으로 탐지된 것을 확인할 수 있습니다.
- 출발지 192.168.11.3에서 목적지 192.168.12.11의 80번 포트(HTTP 서버)로 GET 요청이 전달되었습니다.
- payload 내부에 GET 문자열이 포함되어 있어 Rule이 정상적으로 동작한 상태입니다.

---

### FTP ROOT Detect

- 다음 Rule은 FTP 접속 과정에서 root 계정 로그인 시도를 탐지하는 Rule입니다.
```
alert tcp any any -> any 21 (msg:"FTP ROOT"; content:"USER root"; nocase; sid:1000106; rev:1;)
```

- 목적지 21번 포트는 FTP 서비스 포트입니다.
- FTP 접속 과정에서 payload에 USER root 문자열이 포함되면 탐지합니다.
- nocase 옵션이 있으므로 대소문자를 구분하지 않고 탐지합니다.
- 즉, FTP에서 root 계정으로 로그인을 시도하는지 확인하는 Rule입니다.

#### 결과 확인

- 192.168.11.19에서 다음 명령어로 ftp 접속을 시도합니다.
```
ftp 192.168.12.11
```

- IDS 서버에서 다음 명령어로 alert 로그를 확인합니다
```
cat /var/log/snort/alert
```

![44](/KH_Security/Linux/Firewall/img/44.png)

- alert 로그에서 FTP 관련 탐지 로그가 발생한 것을 확인할 수 있습니다.
- 출발지 192.168.11.19에서 목적지 192.168.12.11의 21번 포트로 FTP 접속이 발생했습니다.
- 이 Rule은 단순 FTP 접속 전체가 아니라 payload 안에 USER root 문자열이 포함된 경우를 탐지합니다.

---

### PHF Offset Depth Detect

- content 옵션으로 `cgi-bin/phf` 문자열을 탐지합니다.
```
alert tcp any any -> any 80 (msg:"PHF Offset Depth Detect"; content:"cgi-bin/phf"; offset:4; depth:20; sid:1000107; rev:1;)
```

- 즉, payload의 4byte부터 20byte 범위 안에서만 `cgi-bin/phf` 문자열을 검사합니다.

- 불필요하게 전체 패킷을 검사하지 않고 특정 위치만 확인하여 탐지 정확도를 높이는 Rule입니다.

#### 결과 확인

- 다음 명령어로 웹 서버에 PHF 취약점 경로로 요청을 보냅니다.
```
curl http://192.168.12.11/cgi-bin/phf
```

![45](/KH_Security/Linux/Firewall/img/45.png)

![46](/KH_Security/Linux/Firewall/img/46.png)

- alert 로그에서 PHF Offset Depth Detect가 발생한 것을 확인할 수 있습니다.
- 출발지 192.168.11.19에서 목적지 192.168.12.11의 80번 포트로 요청이 전달되었습니다.
- payload 내부에 `cgi-bin/phf` 문자열이 포함되어 있어 Rule이 정상적으로 탐지되었습니다.

---

### DISTANCE TEST

- 다음과 같은 Rule 설정으로 payload 내부의 문자열 순서와 거리를 검사합니다.
```
alert tcp any any -> any any (msg:"DISTANCE TEST"; content:"ABC"; content:"DEF"; distance:1; sid:1000108; rev:1;)
```

- 먼저 `ABC` 문자열을 탐지합니다.
- 이후 distance:1 옵션을 통해 1byte 뒤부터 `DEF` 문자열을 다시 탐지합니다.
- 즉, `ABC` 다음에 일정 거리 이후 `DEF`가 존재하는지 확인하는 Rule입니다.

#### 결과 확인

- 192.168.11.19에서 다음 명령어로 테스트 패킷을 전송합니다.
```
printf 'GET /ABCXDEF HTTP/1.1\r\nHost: 192.168.12.11\r\n\r\n' | nc 192.168.12.11 80
```

- `ABCXDEF` 형태로 문자열이 포함된 HTTP 요청을 전송합니다.

- IDS 서버에서 다음 명령어로 alert 로그를 확인합니다.
```
cat /var/log/snort/alert
```

![47](/KH_Security/Linux/Firewall/img/47.png)

- alert 로그에서 DISTANCE TEST가 발생한 것을 확인할 수 있습니다.
- `ABC` 이후 1byte 뒤에 `DEF`가 존재하므로 Rule이 정상적으로 탐지되었습니다.
- 즉, 문자열 간의 위치 관계를 기준으로 공격 패턴을 탐지한 실습입니다.

---

### WITHIN TEST

- 다음과 같은 Rule 설정으로 payload 내부의 문자열 범위를 검사합니다.
```
alert tcp any any -> any any (msg:"WITHIN TEST"; content:"ABC"; content:"EFG"; within:10; sid:1000109; rev:1;)
```

- 먼저 `ABC` 문자열을 탐지합니다.
- within:10 옵션을 통해 `ABC` 이후 10byte 이내에서 `EFG` 문자열을 탐지합니다.
- 즉, 지정된 범위 안에 다음 문자열이 존재하는지 확인하는 Rule입니다.

#### 결과 확인

- 192.168.11.19에서 다음 명령어로 within 조건을 만족하는 HTTP 요청을 전송합니다.
```
curl "http://192.168.12.11/ABCD123EFG"
```

- 그 이후 IDS 서버에서 다음 명령어로 alert 로그를 확인합니다.
```
cat /var/log/snort/alert
```

![within](/KH_Security/Linux/Firewall/img/within.png)

- alert 로그에서 WITHIN TEST가 발생한 것을 확인할 수 있습니다.
- `ABC` 이후 지정된 범위 안에 `EFG`가 존재하여 Rule이 정상적으로 탐지되었습니다.
- 즉, 문자열 간의 거리 제한을 통해 더 정밀한 탐지가 가능함을 확인한 실습입니다.

---

## Non-payload 옵션 실습

- Non-payload 옵션은 패킷의 내용(payload)이 아니라 헤더 정보 자체를 검사하는 방식입니다.
- 즉, 패킷 안에 어떤 문자열이 있는지가 아니라
  패킷의 구조, 플래그, 타입, 코드, 방향 등을 기준으로 탐지합니다.

- 다음과 같이 룰을 설정해주었습니다.

![48](/KH_Security/Linux/Firewall/img/48.png)

---

### ICMP Redirect Detect (itype)

- 다음 Rule은 ICMP Type이 5인 패킷을 탐지합니다.
```
alert icmp any any -> any any (msg:"ICMP Redirect"; itype:5; sid:1000201; rev:1;)
```

- ICMP Type 값을 Type 5로 설정하며 ICMP Redirect 패킷입니다.
- 라우팅 경로를 변경시키는 공격 탐지에 자주 사용됩니다.

#### 결과 확인

- Kali(192.168.11.36)에서 다음 명령어로 ICMP Redirect 패킷을 생성합니다.
```
hping3 --icmp --icmptype 5 192.168.12.11
```

![49](/KH_Security/Linux/Firewall/img/49.png)

- IDS 서버에서 로그를 확인합니다.
```
cat /var/log/snort/alert
```

![50](/KH_Security/Linux/Firewall/img/50.png)

- 로그를 보면 `ICMP Redirect`가 정상적으로 탐지된 것을 확인할 수 있습니다.
- 출발지 192.168.11.36에서 목적지 192.168.12.11로 Redirect 패킷이 전달되었습니다.
- 정상적인 환경에서는 ICMP Redirect가 자주 발생하지 않기 때문에  
  보안 장비에서는 의심 트래픽으로 보는 경우가 많습니다.
- 특히 MITM(중간자 공격)이나 라우팅 경로 조작 공격에 활용될 수 있어 주의가 필요합니다.

---

### ICMP Backdoor Detect (icode)

- 다음 Rule은 ICMP Code와 payload 내용을 함께 검사합니다.
```
alert icmp any any -> any any (msg:"ICMP Backdoor : ls"; itype:8; icode:0; content:"ls"; sid:1000202; rev:1;)
```

- ICMP Echo Request(Type 8) 패킷 중 payload에 `ls` 문자열이 포함된 경우 탐지합니다.
- ICMP 은닉 채널 및 백도어 통신 탐지에 사용됩니다.

#### 결과 확인

- Kali에서 payload 파일을 생성합니다.
```
echo "ls" > payload.txt
```

- 다음 명령어로 payload를 포함한 ICMP 패킷을 전송합니다.
```
hping3 --icmp -d 20 -E payload.txt 192.168.12.11
```

![51](/KH_Security/Linux/Firewall/img/51.png)

- IDS 서버에서 로그를 확인합니다.
```
cat /var/log/snort/alert
```

![52](/KH_Security/Linux/Firewall/img/52.png)

- 로그를 보면 `ICMP Backdoor : ls`가 탐지된 것을 확인할 수 있습니다.
- 출발지 192.168.11.36에서 목적지 192.168.12.11로 ICMP 패킷이 전달되었습니다.
- payload 내부에 `ls` 문자열이 포함되어 있어 Rule이 정상적으로 동작한 상태입니다.

---

### Land Attack Detect (sameip)

- 다음 Rule은 출발지 IP와 목적지 IP가 동일한 패킷을 탐지합니다.
```
alert ip any any -> any any (msg:"Land Attack Detect"; sameip; sid:1000203; rev:1;)
```

- sameip는 src ip와 dst ip가 같은 경우를 의미합니다.
- 대표적인 Land Attack 탐지 방식입니다.

#### 결과 확인

- Kali에서 다음 명령어로 Land Attack 형태의 패킷을 생성합니다.
```
hping3 -a 192.168.12.11 192.168.12.11 --icmp
```

![53](/KH_Security/Linux/Firewall/img/53.png)

- IDS 서버에서 로그를 확인합니다.
```
cat /var/log/snort/alert
```

![54](/KH_Security/Linux/Firewall/img/54.png)

- Land Attack Detect 로그가 발생하면 정상 탐지된 상태입니다.
- 로그를 보면 출발지와 목적지가 모두 192.168.12.11로 동일한 것을 확인할 수 있습니다.
- Land Attack은 자기 자신에게 패킷을 보내도록 위장하여  
  시스템 자원을 소모시키거나 서비스 장애를 유발하는 공격 방식입니다.

---

### SEQ / ACK Detect

- 다음 Rule은 TCP Sequence Number와 ACK Number를 검사합니다.
```
alert tcp any any -> any any (msg:"SEQ 0"; seq:0; sid:1000207; rev:1;)
alert tcp any any -> any any (msg:"ACK 0"; ack:0; sid:1000208; rev:1;)
```

- seq는 TCP Sequence 번호입니다.
- ack는 TCP ACK 번호입니다.
- 비정상적인 TCP 패킷 탐지에 사용됩니다.

#### 결과 확인

- IDS 서버에서 로그를 확인합니다.
```
cat /var/log/snort/alert
```

![55](/KH_Security/Linux/Firewall/img/55.png)

- 로그를 보면 `ACK 0`과 `SEQ 0`가 모두 탐지된 것을 확인할 수 있습니다.
- 이는 비정상적인 TCP 세션 또는 스캔 트래픽이 발생했음을 의미합니다.
- 특히 포트 스캔이나 패킷 생성 도구를 이용한 테스트에서 자주 나타나는 패턴입니다.

---

### XMAS Scan Detect (flags)

- 다음 Rule은 FUP 플래그가 설정된 패킷을 탐지합니다.
```
alert tcp any any -> any any (msg:"XMAS Packet Detected"; flags:FUP; sid:1000206; rev:1;)
```

- `flags:FUP`는 FIN + URG + PSH 플래그가 동시에 설정된 패킷을 의미합니다.
- 이 형태는 크리스마스트리처럼 여러 플래그가 켜져 있어 XMAS Scan이라고 부릅니다.
- 주로 공격자가 방화벽 우회나 포트 스캔을 위해 사용하는 비정상적인 탐색 방식입니다.

#### 결과 확인

- Kali에서 다음 명령어로 XMAS Scan을 수행합니다.
```
nmap -sX 192.168.12.11
```

- IDS 서버에서 로그를 확인합니다.
```
cat /var/log/snort/alert
```

![56](/KH_Security/Linux/Firewall/img/56.png)

- 로그를 보면 `XMAS Packet Detected`가 정상적으로 탐지된 것을 확인할 수 있습니다.
- 출발지 192.168.11.36에서 목적지 192.168.12.11의 여러 포트로 스캔이 발생했습니다.
- 특히 3306(MySQL), 993(IMAPS), 22(SSH), 25(SMTP) 등
  다양한 서비스 포트를 대상으로 탐색이 수행된 것을 볼 수 있습니다.
- 함께 `ACK 0` 로그도 같이 발생하는데, 이는 비정상적인 TCP 세션 패턴이 함께 탐지된 것입니다.

---

### NULL Scan Detect (flags)

- 다음 Rule은 TCP Flag가 없는 NULL Scan 형태의 패킷을 탐지합니다.
```
alert tcp any any -> any any (msg:"NULL Packet Detected"; flags:0; sid:1000205; rev:1;)
```

- `flags:0`은 TCP Flag가 아무것도 설정되지 않은 패킷을 의미합니다.
- 일반적인 TCP 통신에서는 SYN, ACK 등의 플래그가 반드시 존재하므로  
  이런 형태는 매우 비정상적인 패킷입니다.

- 공격자는 방화벽 우회나 포트 상태 확인을 위해 NULL Scan을 자주 사용합니다.

#### 결과 확인

- Kali에서 다음 명령어로 NULL Scan을 수행합니다.
```
nmap -sN 192.168.12.11
```

- IDS 서버에서 로그를 확인합니다.
```
cat /var/log/snort/alert
```

![57](/KH_Security/Linux/Firewall/img/57.png)

- 로그를 보면 `NULL Packet Detected`가 정상적으로 탐지된 것을 확인할 수 있습니다.
- 출발지 192.168.11.36에서 목적지 192.168.12.11의 다양한 포트로 NULL Scan이 수행되었습니다.

---

### FIN Scan Detect (flags)

- 다음 Rule은 FIN Scan 형태의 패킷을 탐지합니다.
```
alert tcp any any -> any any (msg:"FIN Scan Detected"; flags:F; sid:1000204; rev:1;)
```

- `flags:F`는 FIN 플래그만 설정된 패킷을 의미합니다.
- 정상적인 연결 종료 과정에서는 FIN이 사용되지만, 스캔에서는 연결 없이 FIN만 보내 포트 상태를 확인합니다.

#### 결과 확인

- Kali에서 다음 명령어로 FIN Scan을 수행합니다.
```
nmap -sF 192.168.12.11
```

- IDS 서버에서 로그를 확인합니다.
```
cat /var/log/snort/alert
```

![58](/KH_Security/Linux/Firewall/img/58.png)

- 로그를 보면 `FIN Scan Detected`가 정상적으로 탐지된 것을 확인할 수 있습니다.
- 출발지 192.168.11.36에서 목적지 192.168.12.11의 여러 포트로 FIN Scan이 수행되었습니다.
- 함께 `ACK 0` 로그도 같이 발생하는데, 이는 비정상적인 TCP 세션 패턴이 함께 탐지된 것입니다.

---

## Flow 옵션 실습

- flow는 패킷의 방향과 연결 상태를 기준으로 탐지합니다.

### 주요 옵션

- to_server : 클라이언트 → 서버 방향
- to_client : 서버 → 클라이언트 방향
- established : TCP 3-way Handshake 완료 후만 탐지
- stateless : 세션 상태 무시

---

### FTP Login Success Detect

- 다음 Rule은 FTP 로그인 성공 메시지를 탐지하는 Rule입니다.
```
alert tcp any 21 -> any any (msg:"FTP Login Success"; flow:to_client,established; content:"Login successful"; sid:1000302; rev:1;)
```

- 목적지 포트가 아닌 출발지 포트가 21번이므로 FTP 서버가 클라이언트에게 보내는 응답 패킷을 검사합니다.
- `flow:to_client,established` 옵션을 사용하여
  정상적으로 연결이 완료된 상태에서 서버 → 클라이언트 방향의 패킷만 탐지합니다.
- `content:"Login successful"` 조건을 통해 실제 로그인 성공 메시지가 포함된 경우만 탐지합니다.

#### 결과 확인

- EXTERNAL 서버에서 다음 명령어를 실행합니다.
```
ftp 192.168.12.11
```

- 다음과 같은 화면으로 로그인 성공한 것을 알 수 있습니다.

![59](/KH_Security/Linux/Firewall/img/59.png)

- IDS 서버에서 로그를 확인합니다.
```
cat /var/log/snort/alert
```

![60](/KH_Security/Linux/Firewall/img/60.png)

- 로그를 보면 `FTP Login Success`가 정상적으로 탐지된 것을 확인할 수 있습니다.
- 출발지 192.168.12.11의 21번 포트(FTP 서버)에서 목적지 192.168.11.19(클라이언트)로 응답이 전달되었습니다.
- 즉, FTP 서버가 클라이언트에게 로그인 성공 메시지를 보낸 것을 Snort가 탐지한 것입니다.

---

### HTTP GET Detect

- 다음 Rule은 웹 서버로 전달되는 HTTP GET 요청을 탐지하는 Rule입니다.
```
alert tcp any any -> any 80 (msg:"HTTP GET to Server"; flow:to_server,established; content:"GET"; sid:1000301; rev:1;)
```

- 목적지 포트가 80번이므로 웹 서버(HTTP 서버)로 들어가는 요청을 검사합니다.
- `flow:to_server,established` 옵션을 사용하여
  정상적으로 TCP 연결이 완료된 상태에서 클라이언트 → 서버 방향의 패킷만 탐지합니다.
- `content:"GET"` 조건을 통해 HTTP 요청 중 GET 방식의 요청만 탐지합니다.

#### 결과 확인

- EXTERNAL 서버(192.168.11.19)에서 AP 서버(192.168.12.11)의 웹 서비스에 접속합니다.
- 또는 브라우저에서 직접 접속해도 동일하게 테스트할 수 있습니다.
```
curl http://192.168.12.11
```

- IDS 서버에서 로그를 확인합니다.
```
cat /var/log/snort/alert
```

![61](/KH_Security/Linux/Firewall/img/61.png)

- 로그를 보면 `HTTP GET to Server`가 정상적으로 탐지된 것을 확인할 수 있습니다.
- 출발지 192.168.11.19에서 목적지 192.168.12.11의 80번 포트(HTTP 서버)로 GET 요청이 전달되었습니다.
- 즉, 클라이언트가 웹 서버에 접속하면서 발생한 정상적인 HTTP 요청을 Snort가 탐지한 것입니다.

---

### DNS UDP 53 Detect

- 다음 Rule은 UDP 53번 포트로 전달되는 DNS 요청을 탐지하는 Rule입니다.
```
alert udp any any -> any 53 (msg:"DNS UDP 53 Detect"; flow:stateless; sid:1000304; rev:1;)
```

- 목적지 포트가 53번이므로 DNS 서버로 전달되는 질의(Query) 패킷을 검사합니다.
- DNS는 주로 UDP 기반으로 동작하므로 `protocol`을 udp로 설정합니다.
- `flow:stateless` 옵션을 사용하여 세션 연결 여부와 관계없이 패킷 자체를 탐지합니다.
- UDP는 TCP처럼 3-way Handshake가 없기 때문에 stateless 방식으로 탐지하는 것이 일반적입니다.

#### 결과 확인

- EXTERNAL 서버(192.168.11.19)에서 외부 DNS 서버(8.8.8.8)를 대상으로 도메인 조회를 수행합니다.
- 해당 명령어를 실행하면 DNS Query가 UDP 53번 포트로 전송됩니다.
```
nslookup google.com 8.8.8.8
```

- IDS 서버에서 로그를 확인합니다.
```
cat /var/log/snort/alert
```

![62](/KH_Security/Linux/Firewall/img/62.png)

- 로그를 보면 `DNS UDP 53 Detect`가 정상적으로 탐지된 것을 확인할 수 있습니다.
- 출발지 192.168.11.19에서 목적지 8.8.8.8의 53번 포트(DNS 서버)로 UDP 패킷이 전달되었습니다.

---

## 요약 정리

- 이번 실습은 Snort Rule을 이용해 Payload, Non-payload, Flow 기반 탐지를 확인한 내용입니다.
- Payload 실습에서는 HTTP GET, FTP ROOT, PHF, DISTANCE, WITHIN처럼 패킷 내부 문자열을 기준으로 탐지했습니다.
- Non-payload 실습에서는 ICMP Redirect, ICMP Backdoor,  
  Land Attack, SEQ/ACK, XMAS/NULL/FIN Scan처럼 패킷 헤더와 플래그를 기준으로 탐지했습니다.
- Flow 실습에서는 FTP 로그인 성공, HTTP GET 요청, DNS 요청처럼  
  통신 방향과 연결 상태를 기준으로 탐지했습니다.
- 전체적으로 Rule 작성 → 공격/접속 발생 → alert 로그 확인 흐름으로 Snort의 탐지 방식을 확인한 실습입니다.
