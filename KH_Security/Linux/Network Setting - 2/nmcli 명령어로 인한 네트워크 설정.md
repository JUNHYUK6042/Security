# nmcli 명령어로 인한 네트워크 설정

## 네트워크 도구 비교

| 구분 | nmcli | ifconfig |
| ----- | ----- | ----- |
| 패키지 | NetworkManager | net-tools |
| 설치 | 기본 설치 | 설치가 필요할 수 있음 |
| 기능 | 프로파일 기반 설정 및 내용 수정 | 추가적인 명령어 필요 |

### nmcli

- 네트워크 또는 NIC의 재 로드가 필요하다

### ifconfig

- net-tools 패키지를 추가해야 사용 가능하다
- 항구적인 수정은 아니기 때문에 수정된 설정 값은 별도의 저장이 되지 않으면 시스템 재 부팅시에 사라집니다.

---

## nmcli 기본 명령 구조
```text
nmcli [옵션] <object> <command> [arguments]
```
### object 종류

- connection : 네트워크 연결(프로파일) 관리
- device : 실제 네트워크 장치 관리
- general : NetworkManager 전반 상태
- networking : 네트워크 기능 전체 on/off
- radio : Wi-Fi, WWAN 제어
- agent : 인증 처리
- monitor : 상태 변화 실시간 감시

### command 예시
```text
status, show, up, down, on, off, modify, add, delete
```
- nmcli 명령어도 ip명령과 동일하게 축약이 가능합니다.

---

## nmcli 주요 옵션

- -t : 간결한 출력 (terse)
- -p : 사람이 읽기 좋은 출력 (pretty)
- -f : 출력 필드 지정
- -g : 특정 필드만 출력
- -w <초> : 대기 시간 설정
- -a : 상호작용 모드

---

## 설정 파일 경로

`/etc/sysconfig/network-scripts/ifcfg-[NIC]`
- network-scripts 방식의 설정 파일로 Network 서비스에 의해 사용되었던 설정 파일
- RockyLinux8에서는 하위 호환을 위해 사용함.
- 레거시 포맷이므로 이후 버전에서도 NetworkManager에서 우선하지 않지만 발견되면 사용 가능함.

`/etc/NetworkManager/system-connections/[NCI].nmconnection`
- NetworkManager의 프로파일입니다.
- RockyLinux9부터 전용으로 사용됩니다.

---

## 기본정보 확인

### Connection 정보 확인

`nmcli c show [NIC]`

![01]()

---

### Device 정보 확인

`nmcli d show [NIC]

![02]()

---

## nmcli d 와 nmcli d show 차이

- `nmcli d`
  - 장치 목록 요약 출력
  - 여러 device를 한 줄씩 간단히 보여줍니다.
  - 관리 상태를 빠르게 확인하는 용도입니다.

- `nmcli d show`
  - **특정 device의 상세 정보 출력**
  - 실제 인터페이스에 **현재 적용 중인 런타임 상태**를 보여줍니다.
  - IP, Gateway, DNS, MTU 등 세부 정보 확인용입니다.

---

## IP / Gateway / DNS 확인
```text
nmcli d show ens224 | grep IP4.ADDRESS  
nmcli d show ens224 | grep IP4.GATEWAY  
nmcli d show ens224 | grep IP4.DNS  
```
![03]()

---

## IP 주소 변경 (영구 설정)
```text
nmcli c mod ens224 ipv4.addresses 192.168.11.66/24
```
- 이 동작은 **설정 파일을 수정하는 영구 변경**입니다.

---

## 변경 후 상태 및 설정 파일 확인
```text
nmcli d show ens224 | grep IP4.ADDRESS
```
![]()

- nmcli d show는 **현재 세션에 적용된 상태**를 보여주기 때문에 이전 IP가 보입니다.

### 설정 파일 확인

```text
cat /etc/sysconfig/network-scripts/ifcfg-ens224 | grep IPADDR
```

![]()

---

## 설정 적용 방법

- 방법 : 시스템 재부팅 및 connection 재적용

```text
nmcli c up ens224
```
---

## 적용 후 확인
```text
nmcli d show ens224 | grep IP4.ADDRESS
```
![07](/Linux/Network%20Setting%20-%20Part%202/imgs/07.png)

- 변경된 IP로 적용된 것을 확인할 수 있습니다.

---

## connection 기준 확인
```text
nmcli c show ens224 | grep -e IP4.ADDRESS -e ipv4.adress
```
![]()

- connection에 저장된 **영구 설정값**을 확인합니다.

---

## Gateway / DNS 변경

### 변경 전 상태 확인
```text
cat /etc/sysconfig/network-scripts/ifcfg-ens224 | grep -e GATEWAY -e DNS
```
![]()

- Gateway : 
- DNS : 8.8.8.8

---

### 설정 변경
```text
nmcli c mod ens160 ipv4.gateway 192.168.10.254 ipv4.dns 1.1.1.1
```
---

### 설정 파일 확인

![10](/Linux/Network%20Setting%20-%20Part%202/imgs/10.png)

- 영구 설정 파일에는 정상 반영되어 있습니다.
- 시스템에서도 반영이 되기 위해서는 재부팅을 해주면 됩니다.

---

### 현재 상태 확인 (아직 미적용)
```text
nmcli d show ens160 | grep -e IP4.GATEWAY -e IP4.DNS
```
![11](/Linux/Network%20Setting%20-%20Part%202/imgs/11.png)

- 이유
  - nmcli d는 현재 동작 중인 상태만 출력합니다.

### 설정 적용
```text
nmcli c up ens160
```
![12](/Linux/Network%20Setting%20-%20Part%202/imgs/12.png)

- Gateway 및 DNS가 정상적으로 적용된 것을 확인할 수 있습니다.

---

### 라우팅 정보 추가
- 다음 명령어로 현재 NIC 설정을 파일로 저장해두는 위치를 살펴보고, 라우팅 경로를 추가해봅니다.
```text
ls /etc/sysconfig/network-scripts/
```
```text
nmcli con mod ens224 +ipv4.routes '10.0.0.0/24 192.168.11.254'
```
![13](/Linux/Network%20Setting%20-%20Part%202/imgs/13.png)

- NIC가 2개가 있으므로 총 2개가 출력됩니다.
- 하지만 추가한 라우팅 정보가 `/etc/sysconfig/network-scripts/`밑에 파일이 새로 생깁니다.

### 추가된 파일 확인
- 확인해보면 필자가 추가한 정보가 다음과 같이 파일에 기입된 것을 확인할 수 있습니다.

![14](/Linux/Network%20Setting%20-%20Part%202/imgs/14.png)

- 현재 세션에 적용시키기 위해서 `nmcli connection up ens24`로 설정을 현재 세션에 적용시켜줍니다.

![15](/Linux/Network%20Setting%20-%20Part%202/imgs/15.png)

- `ip route | grep 10.0.0.0`으로 라우팅 정보를 찾아보면 다음과 같이 잘 적용된 것을 확인할 수 있습니다.
