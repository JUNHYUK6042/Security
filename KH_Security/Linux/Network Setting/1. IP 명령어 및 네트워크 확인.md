# IP 명령어 및 네트워크 확인
- 기존 ifconfig, route 명령어를 대체하는 `ip 명령어`를 중심으로 정리합니다.

---

## ip 명령어 기본 구조
`ip[옵션] <object> <command> [arguments]`
- objet 하위 명령
  - link
  - address
  - route
  - neighbor
- command
  - show
  - set
  - add
  - del
 
---

## objet와 command
### Link(l)
- 지정한 인터페이스나 또는 모든 인터페이스의 링크 상태 정보 확인

| 명령어 | 설명 |
| --- | --- |
| ip link show | 모든 인터페이스 링크 상태 확인 |
| ip link set [ NIC ] up | [ NIC ] 인터페이스 활성화 |
| ip link set [ NIC ] down | [ NIC ] 인터페이스 비활성화 |

---

### Address(a)
-  모든 인터페이스의 IP설정등 다양한 설정을 확인

| 명령어 | 설명 |
| --- | --- |
| ip address(a) show | 모든 인터페이스의 IP 설정 확인 |
| ip a add 1.2.3.4/24 dev ens160 | IP 주소 추가 |
| ip a add 1.2.3.4/24 dev ens160 | IP 주소 삭제 |

---

### Route(r)
- 설정된 라우팅 정보 확인

| 명령어 | 설명 |
| --- | --- |
| ip route(r) show | 전체 라우팅 테이블 확인 |
| ip route(r) show default | Default Gateway 확인 |
| ip route(r) add 2.1.1.0/24 via 1.1.1.254 dev ens160 | 라우트 추가 |
| ip route(r) del 2.1.1.0/24 | 라우트 삭제 |

---

### Neighbor(n)
- ARP / NDP 테이블 관리

| 명령어 | 설명 |
| --- | --- |
| ip neighbor(n) show | 이웃 테이블 확인 |
| ip neighbor(n) add 1.2.3.4 lladdr 02:02:02:02:02:02 dev ens160 | ARP 항목 추가 |
| ip neighbor(n) del 1.2.3.4 dev ens160 | ARP 항목 삭제 |

---

### IP 명령어 옵션

| 옵션 | 설명 | 예시 |
| --- | --- | --- |
| -s | 통계 정보 확인 | ip -s link show ens160 |
| -d | 상세한 정보 출력 | ip -d address(a) show |
| -4 | IPv4만 적용 | ip -4 address(a) show |
| -6 | IPv6만 적용 | ip -6 address(a) show |
| -c | 컬러 출력 | ip -c address(a) show |
| -o | 한 줄로 출력 | ip -o route(r) show |
| -br | 필수 정보만 간략히 출력 | ip -br route(r) show |

---

## 네트워크 확인

### 인터페이스 및 기본정보 확인

| 명령어 | 설명 |
| --- | --- |
| ip address | 모든 인터페이스 IP 정보 확인 |
| ip a show dev ens160 | 특정 NIC 설정 정보 확인 |
| ip link show | 인터페이스 링크 상태 확인 |
| ip link show ens160 | 특정 인터페이스 링크 상태 확인 |

---

### Default Gateway 확인 (라우팅 정보 확인)

| 명령어 | 설명 |
| --- | --- |
| ip route show default | Default Gateway 확인 |
| ip route | 전체 라우팅 정보 확인 |

---

### Local DNS 확인
- 시스템에서 사용하는 DNS 서버 정보는 resolv.conf 파일을 통해 확인합니다.

| 명령어 | 설명 |
|---|---|
| cat /etc/resolv.conf | 로컬 DNS 설정 확인 |

---

## TCP/IP 설정 파일

### 네트워크 설정 파일
- NetworkManager가 활성화 된 경우 직접 편집하지 않는다.

| 파일경로 | 설명 | 적용 범위 |
| --- | --- | --- |
| /etc/sysconfig/network-scripts/ifcfg-NIC | 네트워크 인터페이스 설정 파일 | ~ Linux8 |
| /etc/NetworkManager/system-connections/NIC.nmconnection | NetworkManager 네트워크 인터페이스 설정 파일 | Linux9 ~ |
| /etc/resolv.conf | DNS Server 설정 파일 | - |
| /etc/hostname | Hostname 설정 파일 | - |

- 네트워크 설정 파일에는 IP Address, Netmask, Gateway 정보가 포함됩니다.
- resolv.conf 파일에는 DNS Server 정보가 설정됩니다.
- Hostname 파일에는 시스템의 Hostname이 설정됩니다.

---

### 추가 설정 파일

| 파일경로 | 설명 |
| --- | --- |
| /etc/sysconfig/network | Hostname, Gateway, NOZEROCONF 설정 |
- Zero Configuration Networking을 위하여 예약된 subnet 설정을 제거한다.
- 반드시 필요한 옵션은 아니다.

---

## TCP/IP 설정 파일 예시

- /etc/sysconfig/network-scripts/ifcfg-ens160 (~Linux8)
  - BOOTPROTO
  - DEVICE
  - ONBOOT
  - IPADDR
  - PREFIX
  - GATEWAY
  - DNS


