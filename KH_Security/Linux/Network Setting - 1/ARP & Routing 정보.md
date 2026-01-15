# Routing 정보 추가 & ARP

---

## ARP 테이블 확인

- ARP(Neighbor) 테이블 확인 명령어는 다음과 같습니다.
```text
ip neighbor show  
또는 축약형  
ip n
```
![19](/KH_Security/Linux/Network%20Setting%20-%201/img/19.png)

---

## ARP 엔트리 해석
```text
192.168.10.125 dev ens160 lladdr 3c:7c:3f:7f:4c:d6 REACHABLE
```
- 의미  
  192.168.10.125은 ens160 인터페이스에 연결된 장비이며  
  MAC 주소는 3c:7c:3f:7f:4c:d6이고  
  현재 통신 가능한 상태입니다.

---

## ARP 명령어 정리

| 구분 | 명령어 |
|---|---|
| 전체 ARP 테이블 확인 | ip neighbor show / ip n |
| 특정 인터페이스 ARP 확인 | ip n show dev [NIC] |
| 특정 IP ARP 확인 | ip n show [IP] |
| ARP 엔트리 수동 추가 | ip n add [IP] lladdr [MAC] dev [NIC] |
| 특정 ARP 엔트리 삭제 | ip n del [IP] dev [NIC] |
| ARP 테이블 전체 초기화 | ip n flush all |
| 특정 인터페이스 ARP 초기화 | ip n flush dev [NIC] |

---

## ARP 엔트리 수동 추가

- 다음과 같이 ARP 정보를 수동으로 추가합니다.
```text
ip n add 192.168.11.200 lladdr 00:1c:2d:3e:4f:05 dev ens224
```
![20](/KH_Security/Linux/Network%20Setting%20-%201/img/20.png)

- `ip n`으로 다시 확인 시  
  수동으로 추가한 ARP 정보가 등록된 것을 확인할 수 있습니다.

---

## ARP 엔트리 삭제

- 다음 명령어로 ARP 엔트리를 삭제합니다.
```text
ip n del 192.168.11.200 dev ens224
```
![21](/KH_Security/Linux/Network%20Setting%20-%201/img/21.png)

- `ip n`으로 재확인 시  
  해당 ARP 엔트리가 정상적으로 삭제된 것을 확인할 수 있습니다.

---

## 라우팅 테이블 확인

- 라우팅 테이블 확인 명령어는 다음과 같습니다.
```text
ip route show  
또는 축약형  
ip r
```
![22](/KH_Security/Linux/Network%20Setting%20-%201/img/22.png)

- 라우팅 테이블을 확인합니다.
- 첫 번째 줄에는 인터페이스에 해당하는 디폴트 게이트웨이 정보가 출력됩니다.
- 각 라우트는 “어떤 네트워크로 가기 위해 어떤 인터페이스를 사용하며, 출발지 IP를 무엇으로 사용할지”를 의미합니다.

---

## Default Route 해석
```text
default via 192.168.10.1 dev ens160 proto static metric 103
```
- 의미  
  목적지를 모르는 모든 트래픽은  
  192.168.10.1로, ens160 인터페이스를 통해 전송됩니다.

---

## 특정 네트워크 Route 해석
```text
192.168.10.0/24 dev ens160 proto kernel scope link src 192.168.10.126 metric 103
```
- 의미  
  192.168.10.0/24 네트워크로 가는 패킷은  
  ens160 인터페이스를 사용하며  
  출발지 IP는 192.168.10.126을 사용합니다.

---

## 라우팅 테이블 수동 변경 (임시)

- 라우트 추가 및 삭제 명령 형식은 다음과 같습니다.
```text
ip route add/del [target ip/mask] via [gateway ip] dev [NIC]
```
- 해당 설정은 **임시 설정**입니다.

---

## Default Gateway 삭제 및 추가

- 기존 Default Gateway 삭제
```text
ip route del default
ip r
```
![23](/KH_Security/Linux/Network%20Setting%20-%201/img/23.png)

- 새로운 Default Gateway 추가
```text
ip route add default via 192.168.10.1 dev ens160
```
![24](/KH_Security/Linux/Network%20Setting%20-%201/img/24.png)

- `ip r`로 확인 시 정상적으로 추가된 것을 확인할 수 있습니다.

---

## 특정 경로(Route) 추가
```text
ip route add 192.168.2.126 via 192.168.11.1 dev ens160
```
![25](/KH_Security/Linux/Network%20Setting%20-%201/img/25.png)

- 특정 목적지로 가는 경로가 정상적으로 추가된 것을 확인할 수 있습니다.

---

## /etc/hosts 파일

- `/etc/hosts` 파일은 호스트명과 IP 주소를 매핑하는 파일입니다.
- 각 레코드는 다음 형식으로 정의됩니다.
```text
[ip] [호스트명(도메인)] [별명]
```
- Name Resolution 순서는 `/etc/host.conf` 파일에서 정의됩니다.

---

## /etc/host.conf 역할

- name resolution 순서를 정의하는 파일입니다.
- 주요 항목은 다음과 같습니다.
```text
hosts : /etc/hosts 파일 검사  
bind  : /etc/resolv.conf에 지정된 DNS 서버 질의
```
---

## /etc/hosts 실습 예시

- `www.google.com`을 자주 사용하는 경우  
  ` vi /etc/hosts` 명령어를 통해 파일에 별명을 등록하여 간편하게 사용할 수 있습니다.


- 이후 다음과 같은 명령어를 입력하면 됩니다.
```text
ping g
```

---

## 네트워크 영구 설정 파일

- `/etc/sysconfig/network-scripts/ifcfg-ens224` 파일은  
  ens224 인터페이스의 IP, Gateway, 부팅 시 동작을 정의하는 **영구 설정 파일**입니다.

![26](/KH_Security/Linux/Network%20Setting%20-%201/img/26.png)

---

## IP 주소 영구 변경 실습

- 기존 IPADDR 값은 `192.168.11.136` 입니다.
- 이를 `192.168.11.137`으로 변경합니다.

![27](/KH_Security/Linux/Network%20Setting%20-%201/img/27.png)

- 시스템 재부팅 후 다음 명령어로 확인합니다.
```text
ip a | grep ens224
```
![28](/KH_Security/Linux/Network%20Setting%20-%201/img/28.png)

- 변경한 IP가 유지되는 것을 확인할 수 있습니다.
- 이 방식은 **재부팅 후에도 유지되는 영구 설정**입니다.

---
