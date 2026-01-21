# CRON & RSYNC

## CRON 개요

- CRON은 주기적으로 실행할 작업을 스케줄에 등록하여 자동으로 실행하는 데몬이다.  
- `at`, `anacron`과 같은 작업 스케줄러 계열에 속한다.

### 관련 파일

| 경로 | 설명 |
|---|---|
| /usr/lib/systemd/system/crond.service | systemctl 기반 데몬 실행 스크립트 |
| /etc/rc.d/init.d/crond | service 기반 데몬 실행 스크립트 |
| /etc/crontab | 시스템 기본 스케줄 파일 |
| /usr/bin/crontab | 사용자 스케줄 설정 명령 |
| /etc/cron.allow | crontab 사용 허용 계정 |
| /etc/cron.deny | crontab 사용 거부 계정 |

---

## crontab 명령어

### crontab 기본 사용법

| 항목 | 내용 |
|---|---|
| 명령어 | `crontab -u [유저] [옵션]` |
| 설명 | 지정한 유저의 cron 스케줄을 관리 |

---

### crontab 옵션 표

| 옵션 | 기능 설명 |
|---|---|
| -e | 스케줄 등록 (vi 편집기 실행) |
| -l | 등록된 스케줄 확인 (출력) |
| -r | 등록된 스케줄 전체 삭제 (reset) |

---

### crontab 스케줄 형식

| 필드 | 의미 |
|---|---|
| 분 | 실행할 분 |
| 시 | 실행할 시 |
| 일 | 실행할 날짜 |
| 월 | 실행할 월 |
| 요일 | 실행할 요일 |
| 작업내용 | 실행할 명령 |

```text
[분] [시] [일] [월] [요일] [작업내용]
```

---

## crontab 실습

### 스케줄 확인 및 등록

| 명령어 | 설명 |
|---|---|
| `crontab -l` | 등록된 스케줄 확인 |
| `crontab -e` | 스케줄 등록 (vi 편집기 실행) |

**예시**

```
00 0-23 * * * rdate -s time.bora.net- 매시간마다 실행
0-59/10 * * * * chown -R data.st /home/data- 0에서 59분까지 10분마다 실행
0-59/10 * * * * chmod -R 775 /home/data
```

#### 기본 스케줄 등록 파일

```
cat /etc/crontab
```

#### 실행확인

```
cat /var/log/cron
```
---

## RSYNC 개요

- RSYNC는 **두 시스템 간의 디렉토리를 동기화**하는 파일 전송 및 백업 도구이다.  
- 네트워크를 통해 원본 데이터와 백업 데이터를 효율적으로 동일하게 유지할 수 있다.

---

## RSYNC 시스템 구성 예시

| 구분 | 역할 | IP 주소 |
|---|---|---|
| rsync server | 원본 데이터 저장 시스템 | 192.168.10.### |
| rsync client | 백업 데이터 저장 시스템 | 192.168.10.### |

---

## RSYNC 동작 개념

| 항목 | 설명 |
|---|---|
| 동기화 대상 | 지정한 디렉토리 |
| 서버 역할 | 일반적으로 원본 저장 시스템 |
| 클라이언트 역할 | 백업본 저장 시스템 |
| 특징 | 변경된 파일만 전송하여 효율적 |

> 어떤 시스템이 rsync 서버가 되어도 상관없지만,  
> **일반적으로 원본 데이터를 보유한 시스템을 rsync 서버로 구성**한다.

---

## RSYNC 실습

- `rsync` 설치 확인

![01]()

- `rsync-daemon.noarch` 파일이 설치되어있지 않으므로  
`dnf install -y rsync-daemon.noarch` 명령어로 설치 해줍줍니다.

![02]()  
![03]()

- 설치가 되어있는 것을 확인할 수 있습니다.

### 관련 파일  

- **데몬 실행 파일**: `/usr/bin/rsync`  
- **관리 스크립트**: `/usr/lib/systemd/system/rsyncd.service`  
- **설정 파일**: `/etc/rsyncd.conf`  

### 데몬 실행

```
systemctl [start | stop | restart | status]  rsyncd.service
```

---

## /etc/rsyncd.conf 파일 설정

- Rsync 데몬의 설정 파일로, 서버에서 공유할 리소스와 접근 권한을 정의하고  
클라이언트가 이 파일을 참고해 접근하고 백업/동기화를 수행합니다.


| 항목 | 설명 |
| --- | --- |
| **서비스 명** | 리소스 식별자, 클라이언트에서 이용한다. |
| **path** | 백업 경로 |
| **comment** | 주석 |
| **uid** | 전송자 UID |
| **gid** | 전송자 GID |
| **use chroot** | rsync 경로를 외부에서 `/`로 인식한다. |
| **read only** | 읽기 전용으로 접근한다. |
| **hosts allow** | 접속 허용할 호스트 (클라이언트만 지정) |
| **max connections** | 동시 접속자 수 |
| **timeout** | 연결 제한 시간 (초) |
| **ITCLASS** | 리소스 분류 (클라이언트 구분 용도) |

---

### /etc/rsyncd.conf 파일 설정 실습

- 먼저 서버에 `backup` 디렉터리를 생성한 뒤 `a.txt`와 `b.txt`를 만들어줍니다.
그런 다음에 Rsync 데몬 설정 파일을 설정 해줍니다.

![04]()
