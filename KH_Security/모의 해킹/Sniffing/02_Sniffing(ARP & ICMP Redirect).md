# Sniffing (ARP & ICMP Redirect)

---

## DSniff

- 스니핑을 위한 자동화 도구다. 많이 알려진 툴이며, 단순한 스니핑 도구가 아니라
스니핑을 위한 다양한 툴이 패키지처럼 만들어져 있다.

### Dsniff Tool 정리

| Tool 이름 | 기능 |
|-----------|------|
| filesnarf | NFS 트래픽에서 스니프한 파일을 현재 디렉토리에 저장 |
| macof     | 임의의 MAC 주소를 생성하여 스위치 MAC 테이블을 오버플로우시켜 허브처럼 동작하게 만듦 |
| mailsnarf | SNMP 및 POP 트래픽을 스니핑하여 이메일 내용을 확인 |
| msgsnarf  | 채팅 메시지를 스니핑 |
| tcpkill   | 탐지 가능한 TCP 세션을 모두 끊음 |
| tcpnice   | ICMP source quench 메시지를 보내 특정 TCP 연결 속도를 느리게 만듦 |
| arpspoof  | ARP 스푸핑 공격 수행 |
| dnsspoof  | DNS 스푸핑 공격 수행 |
| urlsnarf  | HTTP 트래픽을 스니핑하여 접속한 URL 정보를 출력 |

---

## DSniff 설치

- 다음과 같은 명령어를 입력하여 설치해줍니다.
```
apt install -y epel-release
apt install -y dsniff
```

### Dsniff를 이용한 Telnet 스니핑

- Kali Linux에서 Dsniff를 실행합니다.

![dsniff1](/KH_Security/모의%20해킹/Sniffing/img/01.dsniff.png)

- Windows환경에서 Telnet 서버를 접속 후 종료합니다.

![Telnet](/KH_Security/모의%20해킹/Sniffing/img/Telnet.png)

- 텔넷 서버 접속 종료 후 Kali Linux에서 확인해줍니다.

![dsniff2](/KH_Security/모의%20해킹/Sniffing/img/02.dsniff.png)

- Telnet은 암호화가 없어 dsniff로 사용자의 입력 정보(ID, 명령어 등)가 평문 그대로 노출됩니다.

---

## ARP Redirect

- ARP 리다이렉트 공격은 기본적으로 2계층에서 실시되며, 공격은 위조된 ARP reply 패킷을 보내는 방법을 사용하고,
자신의 MAC 주소가 라우터라며 브로드캐스트를 주기적으로 하는 것입니다.
- ARP 스푸핑는 호스트 대 호스트 공격이며, ARP 리다이렉트는 랜의 모든 호스트 대 라우터라는 것 말고는 큰 차이점은 없으며,
ARP 스푸핑에서와 마찬가지로 공격자 자신만은 원래 라우터의 MAC 주소를 알고 있어야 하고, 받은 모든 패킷은 다시 라우터로 릴레이해 주어야만 합니다.

### Fragrouter
- 스니핑 공격을 보조하는 도구이며, 공격자가 중간자가 되었을 때 패킷이 끊기지 않도록 포워딩 하는 역할입니다.

#### 설치
```
apt install -y fragrouter
```

---

### 실습환경
```
공격자(Kali) : 192.168.11.36, MAC 주소(00-0c-29-8e-f5-9c)
대상(Windows) : 192.168.11.7, MAC 주소(00-0c-29-13-75-ca)
대상(Linux) : 192.168.11.17, MAC 주소(00-0c-29-a4-4a-38)
라우터 : 192.168.11.1, MAC 주소(00-50-56-f6-f0-c3)
```

### ARP Redirect 실습

- Windosw 환경에서 `arp -a` 명령어를 통해 라우팅 테이블을 확인해줍니다.
![01_arpspoof](/KH_Security/모의%20해킹/Sniffing/img/01.arpspoof.png)

- Kali Linux에서 `fragrouter -B1` 명령어를 통해 라우터로 릴레이 시켜줄 보조도구를 실행합니다.
![02_arpspoof](/KH_Security/모의%20해킹/Sniffing/img/02.arpspoof.png)

---

- 그 다음으로 arpspoof를 이용하여 ARP Redirect 공격을 실행합니다.
```
arpspoof -i eth0 -t 192.168.11.7 192.168.11.1

- 192.168.11.7에게 192.168.11.1의 MAC 주소를 00-0c-29-8e-f5-9c라고 알려줍니다.
```
![03_arpspoof](/KH_Security/모의%20해킹/Sniffing/img/03.arpspoof.png)

---

- Windows에서 다시 한번 라우팅 테이블을 확인합니다.

![04_arpspoof](/KH_Security/모의%20해킹/Sniffing/img/04.arpspoof.png)

- 위의 결과같이 192.168.11.1의 MAC 주소가 192.168.11.36의 MAC 주소와 동일해진 것을 확인할 수 있습니다.

---

- 192.168.11.17(Linux)에서도 동일하게 진행했습니다.

- 위와 같이 Linux 환경에서 라우팅 테이블을 확인합니다.

![06_arpspoof](/KH_Security/모의%20해킹/Sniffing/img/06.arpspoof.png)

- 그 다음으로 Linux 환경에도 arpspoof를 이용하여 ARP Redirect 공격을 실행합니다.
```
arpspoof -i eth0 -t 192.168.11.17 192.168.11.1

- 192.168.11.17에게 192.168.11.1의 MAC 주소를 00-0c-29-8e-f5-9c라고 알려줍니다.
```

![05_arpspoof](/KH_Security/모의%20해킹/Sniffing/img/05.arpspoof.png)

- 다시 라우팅 테이블을 확인합니다.

![07_arpspoof](/KH_Security/모의%20해킹/Sniffing/img/07.arpspoof.png)

- 리눅스 환경에서도 192.168.11.1의 MAC주소가 192.168.11.36의 MAC 주소와 동일한 것을 확인할 수 있습니다.

---

## ICMP Redirect

- ICMP Redirect는 라우터가 더 효율적인 경로가 존재할 때,  
  호스트에게 새로운 게이트웨이를 알려주는 기능이다.  
- 이 기능을 악용하면 공격자가 트래픽을 자신의 장비로 우회시켜 스니핑을 수행할 수 있습니다.

### 공격 흐름

1. 공격자가 피해자에게 ICMP Redirect 패킷을 보냅니다.  
2. 피해자는 게이트웨이를 공격자로 변경합니다.  
3. 이후 모든 트래픽이 공격자를 거쳐 라우터로 전달됩니다.  
4. 공격자는 패킷을 가로채서 분석(Sniffing)합니다.

---

### 컴파일

- 다음 명령어를 통해 icmp_redir 파일을 전송합니다.
```bash
  gcc -o icmp_redir icmp_redir.c
```

---

### ICMO_Redir 사용

- 다음 명령어를 통해 ICMP Redirect 공격을 시도합니다.
```
  icmp_redir [게이트웨이] [공격 대상] [외부] [공격자주소]
```

- icmp_redir는 피해자의 게이트웨이를 공격자의 주소로 변경하도록 속여 트래픽을 가로채는 공격 도구입니다.
