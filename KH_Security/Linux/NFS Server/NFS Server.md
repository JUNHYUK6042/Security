# NFS Server

## 개요

- 본 문서는 NFS(Network File System)를 이용하여 서버의 디렉토리를 클라이언트에 공유하는 과정을 실습 중심으로 정리한 문서이다.  
- NFS 서버 구성, 공유 디렉토리 설정, 권한 옵션(root_squash, all_squash 등),  
클라이언트 마운트 과정을 통해 파일 시스템 공유 구조를 이해하는 것을 목표로 한다.
- NFS는 네트워크를 통해 서버의 파일 시스템을 클라이언트에 공유하는 서비스이다.
- Sun Microsystems에서 개발되었으며, 유닉스/리눅스 환경에서 주로 사용된다.
- 애플리케이션 공유가 아닌 **파일 시스템 자체를 공유**한다.

---

## NFS 구성 요소

- NFS Server : 공유 디렉토리를 제공하는 서버
- NFS Client : 서버의 디렉토리를 마운트하여 사용하는 시스템

- 데몬: `/usr/sbin/exportfs`
  - NFS 서버에서 `/etc/exports` 파일에 정의된 공유 디렉토리 정보를 실제로 서버에 반영하고 관리하는 명령입니다.
  - 공유 설정을 적용하거나 현재 export 상태를 관리하는 역할을 합니다.

- 관리 스크립트: `/usr/lib/systemd/system/nfs-server.service`
  - systemd에서 NFS 서버 서비스를 제어하기 위한 서비스 파일입니다.
  - NFS 서버 기능을 시작, 중지, 관리하는 역할을 합니다.
 
- 환경 설정 파일 : `/etc/exports`
  - NFS 서버에서 **어떤 디렉토리를**, **어떤 클라이언트에게**, **어떤 권한으로** 공유할지 정의하는 설정 파일입니다.
  - 기본 형식 : `[export 할 디렉토리] [허가할 클라이언트][(옵션)]`

---

## NFS 패키지 설치

- NFS 서버와 클라이언트 양쪽 모두 다음 패키지를 설치합니다.
```
dnf install -y nfs-utils
```
![01](/KH_Security/Linux/NFS%20Server/img/01.png)  

---

## exports 주요 옵션

| 옵션 | 역할 | 기능 설명 |
|---|---|---|
| `ro` | 읽기 전용 공유 | 클라이언트는 파일 읽기만 가능하고 쓰기는 불가능 |
| `rw` | 읽기/쓰기 허용 | 클라이언트가 파일 생성, 수정, 삭제 가능 |
| `root_squash` | root 권한 제한 | 클라이언트의 root 사용자를 nobody로 매핑하여 서버 보호 |
| `no_root_squash` | root 권한 허용 | 클라이언트 root를 서버 root와 동일하게 취급 (보안 위험) |
| `all_squash` | 전체 권한 축소 | 모든 사용자를 nobody로 매핑하여 공용 디렉토리처럼 사용 |
| `no_all_squash` | 사용자 권한 유지 | 클라이언트 사용자 UID/GID를 그대로 사용 |
| `anonuid` | 대체 UID 지정 | squash 적용 시 사용할 UID를 지정 |
| `anongid` | 대체 GID 지정 | squash 적용 시 사용할 GID를 지정 |
| `sync` | 동기화 쓰기 | 쓰기 작업을 즉시 디스크에 반영하여 데이터 안정성 확보 |

- **일반 유저**
```text
defalut : `no_all_squash`
설정 : `all_squash`
```

- **Root**
```text
default : `root_squash`
설정 : `no_root_squash`
```
 
---

## exports 설정 내용

- 필자는 a1 ~ a8 디렉토리를 생성하고 다음과 같이 설정합니다.
```
/home/a1 192.168.64.26(rw,no_root_squash)  
/home/a2 192.168.64.26(rw,all_squash)  
/home/a3 192.168.64.26(rw,no_all_squash)  
/home/a4 192.168.64.26(rw,all_squash,root_squash)  
/home/a5 192.168.64.26(rw,all_squash,no_root_squash)  
/home/a6 192.168.64.26(rw,no_all_squash,root_squash)  
/home/a7 192.168.64.26(rw,no_all_squash,no_root_squash)  
/home/a8 192.168.64.26(rw,anonuid=5001,anongid=5000)
```

![02](/KH_Security/Linux/NFS%20Server/img/02.png)

### 의미

```text
- a1 (no_root_squash) : root 권한이 서버까지 그대로 전달됩니다.
- a2 (all_squash) : 모든 사용자가 nobody로 처리됩니다.
- a3 (no_all_squash) : 모든 사용자의 UID/GID가 그대로 유지됩니다.
- a4 (all_squash, root_squash) : 모든 사용자가 nobody로 강제 매핑됩니다.
- a5 (all_squash, no_root_squash) : 일반 사용자는 nobody, root만 예외 처리됩니다.
- a6 (no_all_squash, root_squash) : 일반 사용자는 유지, root만 제한됩니다.
- a7 (no_all_squash, no_root_squash) : 모든 권한이 그대로 전달됩니다.
- a8 (anonuid, anongid) : squash 사용자를 특정 계정으로 통제합니다.`
```

---

## NFS 서버 디렉터리

```
mkdir /home/a1 /home/a2 /home/a3 /home/a4 /home/a5 /home/a6 /home/a7 /home/a8
```

![03](/KH_Security/Linux/NFS%20Server/img/03.png)

---

## exports 적용

```text
systemctl start nfs-server.service  
exportfs -rv
```

![04](/KH_Security/Linux/NFS%20Server/img/04.png)

---

## NFS 클라이언트- mount

```text
mount -t nfs NFS_서버_IP:/공유디렉토리 /마운트할디렉토리
```

- NFS 파일 시스템을 클라이언트에 마운트하기 위한 명령이다.
- 일반적인 mount 명령과 동일한 방식으로 사용한다.
- 재귀적인 mount는 허용되지 않는다.
- 마운트된 디렉토리는 서버의 소유자, 그룹 소유자, 퍼미션을 기준으로 인식된다.
- 마운트 중에는 클라이언트의 해당 디렉토리 설정은 가려진다

```text
mount 192.168.64.24:/home/a1 /home/a1  
mount 192.168.64.24:/home/a2 /home/a2  
mount 192.168.64.24:/home/a3 /home/a3  
mount 192.168.64.24:/home/a4 /home/a4  
mount 192.168.64.24:/home/a5 /home/a5  
mount 192.168.64.24:/home/a6 /home/a6  
mount 192.168.64.24:/home/a7 /home/a7  
mount 192.168.64.24:/home/a8 /home/a8  
```

![05](/KH_Security/Linux/NFS%20Server/img/05.png)

---

## root 사용자 및 일반 사용자 파일 생성 후 결과 확인

### root

![06](/KH_Security/Linux/NFS%20Server/img/06.png)

```text
  a1 : no_root_squash 옵션으로 인해 클라이언트 root가 서버 root로 그대로 매핑되어, root 권한으로 설정
  a2 : all_squash로 인해 모든 사용자(root 포함)가 nfsnobody로 매핑되며, other 권한이 적용
  a3 : root_squash 적용으로 인해 nobody로 매핑되고, other 권한이 적용
  a4 : 모두 nobody로 매핑되므로, 둘 다 other 권한으로 적용
  a5 : all_squash 모든 사용자가 nobody로 매핑되기때문에, other 권한으로 적용
  a6 : root_squash로 인해 nobody로 매핑되며 other 권한으로 적용
  a7 : 서버에서 root로 일치시키고 서버에서의 root 권한으로 적용
  a8 : 강제적으로 UID,GID 1000(ast06)으로 강제 매핑하여, ast06 계정 권한으로 적용
```

### 일반 사용자

![07](/KH_Security/Linux/NFS%20Server/img/07.png)

```text
  a1 : 서버 사용자와 일치시키기 때문에 자신의 ast06로 권한 설정
  a2 : all_squash로 인해 모든 사용자(root 포함)가 nfsnobody로 매핑되며, other 권한이 적용
  a3 : no_all_squash로 서버와 클라이언트 사용자를 일치시켜 ast11 계정의 권한 적용
  a4 : 모두 nobody로 매핑되므로, 둘 다 other 권한으로 적용
  a5 : root처럼 nobody로 매핑되어 other 권한으로 적용
  a6 : no_all_squash 서 버의 사용자와 클라이언트의 사용자를 일치시키면서, ast06 계정의 권한으로 적용
  a7 : 서버 사용자와 클라이언트 사용자로 간주되어, ast06 계정 권한으로 적용
  a8 : 강제적으로 UID,GID 1000(ast06)으로 강제 매핑하여, ast06 계정 권한으로 적용
```
