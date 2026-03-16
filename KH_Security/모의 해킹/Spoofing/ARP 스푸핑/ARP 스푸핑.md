# ARP Spoofing

## 개요
- ARP(Address Resolution Protocol)는 IP 주소를 기반으로 해당 장치의 MAC 주소를 알아내기 위한 프로토콜입니다.  
- ARP Spoofing은 공격자가 위조된 ARP Reply 패킷을 전송하여 IP와 MAC 주소의 매핑 정보를 공격자의 MAC 주소로 변조하는 공격입니다.  
  이를 통해 공격자는 네트워크 통신을 중간에서 가로채는 MITM(Man-In-The-Middle) 공격을 수행할 수 있습니다.
- ARP 프로토콜은 IP → MAC 주소 매핑을 위해 사용됩니다.
---

## 실습 도구 설치

```
apt install -y fake
```

![01](/KH_Security/모의%20해킹/Spoofing/ARP%20스푸핑/img/01.png)

- 다음과 같이 fake 도구를 설치 해줍니다.

---

## ARP Spoofing 실습

### 실습 목적
- send_arp 프로그램을 이용하여 ARP 패킷을 위조하고    
피해자의 ARP 테이블이 변경되는 과정과 Wireshark에서 ARP 패킷을 분석합니다.

---

## 실습 환경

| 구분 | IP | MAC |
|---|---|---|
| Windows | 192.168.11.7 | 00:0c:29:f9:78:42 | 
| Linux | 192.168.11.17 | 00:0c:29:a4:4a:38 |
| 공격자 (Kali) | 192.168.11.36 | 00:0c:29:8e:f5:9c |

---

## 공격 전 ARP 테이블 확인

### Windows (192.168.11.7)

```bash
arp -a
```
![02](/KH_Security/모의%20해킹/Spoofing/ARP%20스푸핑/img/02.png)

- 위와 같이 정상적으로 IP와 MAC 주소가 매핑되어 있는지 확인해줍니다.

### Linux (192.168.11.17)

```
ip n
```
![03](/KH_Security/모의%20해킹/Spoofing/ARP%20스푸핑/img/03.png)

- 위와 같이 정상적으로 IP와 MAC 주소가 매핑되어 있는지 확인해줍니다.

---

## ARP Spoofing 공격

![04](/KH_Security/모의%20해킹/Spoofing/ARP%20스푸핑/img/04.png)

- 다음과 같은 명령어를 통해 ARP Spoofing 공격을 시도합니다.
```
send_arp 192.168.11.17 00:0c:29:8e:f5:9c 192.168.11.7 00:0c:29:f9:78:42
-> 192.168.11.7에게 192.168.11.17의 MAC 주소를 00:0c:29:8e:f5:9c라고 알려줍니다.
```
```
send_arp 192.168.11.7 00:0c:29:8e:f5:9c 192.168.11.17 00:0c:29:a4:4a:38
-> 192.168.11.17에게 192.168.11.7의 MAC 주소를 00:0c:29:8e:f5:9c라고 알려줍니다.
```

---

## 공격 후 ARP 테이블 확인

### Windows

![05](/KH_Security/모의%20해킹/Spoofing/ARP%20스푸핑/img/05.png)

-  두 IP 주소가 동일한 MAC 주소로 변경된 것을 확인할 수 있는데,  
이는 ARP Spoofing 공격으로 인해 ARP 테이블이 조작된 상태입니다.

### Linux

![06](/KH_Security/모의%20해킹/Spoofing/ARP%20스푸핑/img/06.png)

- Linux도 마찬가지로 두 IP 주소가 동일한 MAC 주소로 변경된 것을 확인할 수 있는데,  
이는 ARP Spoofing 공격으로 인해 ARP 테이블이 조작된 상태입니다.

---

## Wireshark 패킷 분석 1 (send_arp 192.168.11.7 00:0c:29:8e:f5:9c 192.168.11.17 00:0c:29:a4:4a:38)

- ARP Spoofing 실습 과정에서 발생한 ARP 패킷을 캡쳐하여 분석했습니다.

## ARP Request

![07](/KH_Security/모의%20해킹/Spoofing/ARP%20스푸핑/img/07.png)

### Wireshark Info
- Who has 192.168.11.17? Tell 192.168.11.7
  - 192.168.11.7이 192.168.11.17의 MAC 주소를 알기 위해 ARP Request를 전송한 것이다.

#### 의미
- 192.168.11.7이 “192.168.11.17의 MAC 주소가 뭐냐? 알면 나(192.168.11.7)에게 알려줘” 라고 묻는 패킷이다.

### Ethernet Header 분석
```
- dst MAC : 00:0c:29:a4:4a:38
- src MAC : 00:0c:29:8e:f5:9c
- type : 08 06 → ARP

- 설명
  - Ethernet Type이 `0x0806`이므로 이 프레임은 ARP 패킷이다.  
  - 송신 MAC은 `00:0c:29:8e:f5:9c`이고, 이 장비가 요청을 보낸 장비이다.
```

### ARP Header 분석

- 본 실습에서 캡처한 패킷은 `target MAC address`에 실제 MAC 주소가 포함되어 있어, 일반적인 ARP Request와는 다른 형태로 확인된다.

```
- hardware type (2byte) : 00 01 → Ethernet
- protocol type (2byte) : 08 00 → IPv4
- hardware length (1byte) : 06 → MAC 주소 길이 6byte
- protocol length (1byte) : 04 → IP 주소 길이 4byte
- opcode (2byte) : 00 01 → ARP Request

- sender MAC address (6byte) : 00:0c:29:8e:f5:9c
- sender IP address (4byte) : 192.168.11.7

- target MAC address (6byte) : 00:0c:29:a4:4a:38
- target IP address (4byte) : 192.168.11.17
```

### 핵심 해석
이 패킷에서 가장 중요한 부분은 다음 두 가지이다.

- sender IP = 192.168.11.7
  - 질문을 보낸 장비가 192.168.11.7이다.

- target MAC = 00:00:00:00:00:00
  - 아직 192.168.11.17의 MAC 주소를 모르기 때문에 비워둔 상태이다.

- 즉, 이 패킷은  
**192.168.11.7이 192.168.11.17의 MAC 주소를 알아내기 위해 보낸 ARP 요청 패킷**이다.

---

## ARP Reply

![08](/KH_Security/모의%20해킹/Spoofing/ARP%20스푸핑/img/08.png)

### Wireshark Info
- 192.168.11.17 is at 00:0c:29:a4:4a:38

### 의미
- 192.168.11.17이 “내 MAC 주소는 00:0c:29:a4:4a:38야” 라고 응답하는 패킷이다.


### Ethernet Header 분석
```
- dst MAC : 00:0c:29:8e:f5:9c
- src MAC : 00:0c:29:a4:4a:38
- type : 08 06 → ARP

- 설명
  - 이번에는 송신 MAC이 `00:0c:29:a4:4a:38`이다.
  - 즉, 192.168.11.17이 응답을 보내고 있다는 뜻이다.
```

### ARP Header 분석
```
- hardware type (2byte) : 00 01 → Ethernet
- protocol type (2byte) : 08 00 → IPv4
- hardware length (1byte) : 06 → MAC 주소 길이 6byte
- protocol length (1byte) : 04 → IP 주소 길이 4byte
- opcode (2byte) : 00 02 → ARP Reply

- sender MAC address (6byte) : 00:0c:29:a4:4a:38
- sender IP address (4byte) : 192.168.11.17

- target MAC address (6byte) : 00:0c:29:8e:f5:9c
- target IP address (4byte) : 192.168.11.7
```

### 핵심 해석
- 이 패킷은  
**192.168.11.17이 자신의 MAC 주소(00:0c:29:a4:4a:38)를 192.168.11.7에게 알려주는 ARP 응답 패킷**이다.

- 즉, 192.168.11.7은 이 응답을 받고 ARP 테이블에 다음 정보를 저장할 수 있다.  
192.168.11.17 → 00:0c:29:a4:4a:38

---

## Wireshark 패킷 분석 2 (send_arp 192.168.11.17 00:0c:29:8e:f5:9c 192.168.11.7 00:0c:29:f9:78:42)

### ARP Request

![09](/KH_Security/모의%20해킹/Spoofing/ARP%20스푸핑/img/09.png)

### Wireshark Info
- Who has 192.168.11.7? Tell 192.168.11.17
  - 192.168.11.17이 192.168.11.7의 MAC 주소를 알기 위해 ARP Request를 전송한 것이다.

### 의미
- 192.168.11.17이 “192.168.11.7의 MAC 주소가 뭐냐? 알면 나(192.168.11.17)에게 알려줘” 라고 묻는 패킷이다.

### Ethernet Header 분석

- 일반적인 ARP Request는 브로드캐스트로 전송되지만, 본 실습 캡처에서는 특정 대상 MAC으로 전송되는 형태로 확인됩니다.

```
- dst MAC : 00:0c:29:f9:78:42
- src MAC : 00:0c:29:8e:f5:9c
- type : 08 06 → ARP

- 설명
  - Ethernet Type이 `0x0806`이므로 이 프레임은 ARP 패킷이다.
  - 송신 MAC은 `00:0c:29:8e:f5:9c`이고, 이 장비가 요청을 보낸 장비이다.
```

### ARP Header 분석

- 본 실습에서 캡처한 패킷은 `target MAC address`에 실제 MAC 주소가 포함되어 있어, 일반적인 ARP Request와는 다른 형태로 확인된다.

```
- hardware type (2byte) : 00 01 → Ethernet
- protocol type (2byte) : 08 00 → IPv4
- hardware length (1byte) : 06 → MAC 주소 길이 6byte
- protocol length (1byte) : 04 → IP 주소 길이 4byte
- opcode (2byte) : 00 01 → ARP Request

- sender MAC address (6byte) : 00:0c:29:8e:f5:9c
- sender IP address (4byte) : 192.168.11.17

- target MAC address (6byte) : 00:0c:29:f9:78:42
- target IP address (4byte) : 192.168.11.7
```

### 핵심 해석
이 패킷에서 가장 중요한 부분은 다음 두 가지이다.

- sender IP = 192.168.11.17
  - 질문을 보낸 장비가 192.168.11.17이다.

- target MAC = 00:00:00:00:00:00
  - 아직 192.168.11.7의 MAC 주소를 모르기 때문에 비워둔 상태이다.

- 즉, 이 패킷은  
**192.168.11.17이 192.168.11.7의 MAC 주소를 알아내기 위해 보낸 ARP 요청 패킷**이다.

---

## ARP Reply

![10](/KH_Security/모의%20해킹/Spoofing/ARP%20스푸핑/img/10.png)

### Wireshark Info
- 192.168.11.7 is at 00:0c:29:f9:78:42

### 의미
- 192.168.11.7이 “내 MAC 주소는 00:0c:29:f9:78:42야” 라고 응답하는 패킷이다.

### Ethernet Header 분석

- 일반적인 ARP Request는 브로드캐스트로 전송되지만, 본 실습 캡처에서는 특정 대상 MAC으로 전송되는 형태로 확인됩니다.

```
- dst MAC : 00:0c:29:8e:f5:9c
- src MAC : 00:0c:29:f9:78:42
- type : 08 06 → ARP

- 설명
  - 이번에는 송신 MAC이 `00:0c:29:f9:78:42`이다.
  - 즉, 192.168.11.7이 응답을 보내고 있다는 뜻이다.
```

### ARP Header 분석
```
- hardware type (2byte) : 00 01 → Ethernet
- protocol type (2byte) : 08 00 → IPv4
- hardware length (1byte) : 06 → MAC 주소 길이 6byte
- protocol length (1byte) : 04 → IP 주소 길이 4byte
- opcode (2byte) : 00 02 → ARP Reply

- sender MAC address (6byte) : 00:0c:29:f9:78:42
- sender IP address (4byte) : 192.168.11.7

- target MAC address (6byte) : 00:0c:29:8e:f5:9c
- target IP address (4byte) : 192.168.11.17
```

### 핵심 해석
- 이 패킷은  
**192.168.11.7이 자신의 MAC 주소(00:0c:29:f9:78:42)를 192.168.11.17에게 알려주는 ARP 응답 패킷**이다.

- 즉, 192.168.11.17은 이 응답을 받고 ARP 테이블에 다음 정보를 저장할 수 있다.  
192.168.11.7 → 00:0c:29:f9:78:42

---

### 결론

- ARP Spoofing 공격은 ARP 프로토콜의 인증 부재로 인해 발생합니다.   
- 공격자는 위조된 ARP Reply 패킷을 전송하여 피해자의 ARP Cache를 조작할 수 있습니다.  
이로 인해 공격자는 피해자와 서버 사이에서 패킷을 가로채는 MITM(Man-In-The-Middle) 공격을 수행할 수 있습니다.
