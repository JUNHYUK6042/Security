# NAT 환경 구성

## 개요

- 본 문서는 Rocky Linux 최소 설치 환경에서  
  **다중 네트워크 구간 간 통신을 위한 NAT / 라우팅 실습**을 정리한 문서입니다.
- Linux → Router → Linux 구조를 구성하여 서로 다른 네트워크 간 통신이 가능하게 합니다.
- 방화벽, 커널 포워딩, SELinux, 라우팅 설정을 단계별로 확인합니다.

---

## 네트워크 구성 개요

- 사진 -

---

## Router (ens160) 네트워크 설정

![02](/KH_Security/Linux/NAT/img/02.png)

- IP 주소 : 192.168.11.254
- 서브넷 마스크 : 255.255.255.0  
- 게이트웨이 : 192.168.11.1  
- DNS : 8.8.8.8  

---

## Router (ens###) 네트워크 설정

![03](/KH_Security/Linux/NAT/img/03.png)

- IP 주소 : 192.168.12.1  
- 서브넷 마스크 : 255.255.255.0
- 게이트웨이 : X
- DNS : X

---

## Linux11 네트워크 설정

![04](/KH_Security/Linux/NAT/img/04.png)
- IP 주소 : 192.168.11.127  
- 서브넷 마스크 : 255.255.255.0  
- 게이트웨이 : 192.168.11.1  
- DNS : 8.8.8.8  

---

## Linux12 네트워크 설정

![05](/KH_Security/Linux/NAT/img/05.png)

- IP 주소 : 192.168.12.127
- 서브넷 마스크 : 255.255.255.0  
- 게이트웨이 : 192.168.12.1
- DNS : 8.8.8.8  

---

## firewalld 비활성화

```text
systemctl disable firewalld.service  
systemctl stop firewalld.service  
```
![06](/KH_Security/Linux/NAT/img/06.png)

- 위 두 명령어는 다음을 의미합니다.
  - 현재 실행 중인 firewalld 서비스를 중지합니다.
  - 시스템 재부팅 후에도 firewalld가 자동으로 실행되지 않도록 설정합니다.

---

## IP 포워딩 활성화

- Router 역할을 수행하기 위해 커널 IP 포워딩을 활성화합니다.
- ` vi /etc/sysctl.conf` 명령어를 통해서 파일에 다음 항목을 추가합니다.

`net.ipv4.ip_forward=1`

![07](/KH_Security/Linux/NAT/img/07.png)

- 이 설정은 해당 라우터에서 패킷을 Linux 12 네트워크 대역으로 보내기 위한 설정입니다.

---

## 커널 설정 즉시 적용

`sysctl -p`

![08](/KH_Security/Linux/NAT/img/08.png)

- `/etc/sysctl.conf`에 설정한 커널 옵션을  
  재부팅 없이 즉시 적용합니다.

---

## SELinux 비활성화

- `/etc/selinux/config` 파일을 수정합니다.
```text
SELINUX=disabled  
```
![09](/KH_Security/Linux/NAT/img/09.png)

- SELinux를 완전히 비활성화하여 커널 보안 정책으로 인한 통신 차단을 제거합니다.
- 해당 설정은 재부팅 후 적용됩니다.

---

## Windows (192.168.10.146) 라우팅 추가

- Windows PC에서  
  192.168.12.0/24 네트워크로 통신 가능하도록 라우팅 경로를 수동으로 추가합니다.

`route -p add 192.168.12.0 MASK 255.255.255.0 192.168.11.254`

![10](/KH_Security/Linux/NAT/img/10.png)

- -p 옵션의 의미
  - 시스템 재부팅 후에도 라우팅 정보를 유지합니다.

---

## Linux11 → Linux12 라우팅 추가

- Linux11에서 Linux12 네트워크로 통신 가능하도록 라우팅을 추가합니다.
- 추가를 해주어야 Linux11에서 Linux12로 패킷을 전송할 수 있습니다.

```text
nmcli con mod ens160 +ipv4.routes "192.168.12.0/24 192.168.11.254"
nmcli con up ens160
```
![11](/KH_Security/Linux/NAT/img/11.png)

- `ip route` 확인 시  
  192.168.12.0/24 네트워크로 가는 경로가 정상적으로 추가된 것을 확인할 수 있습니다.

---

## 인터페이스 및 IP 상태 확인

### Linux11

![12](/KH_Security/Linux/NAT/img/12.png)

### Linux12

![13](/KH_Security/Linux/NAT/img/13.png)

### Router

![14](/KH_Security/Linux/NAT/img/14.png)

- 각 시스템의 인터페이스와 IP 상태가 정상임을 확인합니다.

---

## 통신 테스트

### Linux11 -> Linux 12 통신 테스트

```text
ping 192.168.12.127
```  
![15](/KH_Security/Linux/NAT/img/15.png)

- 정상적으로 응답이 수신되는 것을 확인할 수 있습니다.

### Linux12 -> Linux 11 통신 테스트

```text
ping 192.168.11.127
```  
![16](/KH_Security/Linux/NAT/img/16.png)

- Linux12에서도 정상적으로 응답이 수신되는 것을 확인할 수 있습니다.

---
