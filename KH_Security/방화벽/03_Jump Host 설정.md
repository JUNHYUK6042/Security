# MobaXterm Jump Host 설정 정리

## 개요

- 본 설정은 MobaXterm에서 Jump Host(SSH Gateway)를 이용하여  
중간 서버를 거쳐 최종 서버까지 한 번에 접속하는 방법입니다.

---

## Jump Host 접근을 위한 방화벽 룰 설정

- 외부(main)에서 내부 서버로 직접 접근을 차단하고,  
  반드시 Bastion Host(d_web)를 경유하여 접근하도록 하기 위해 다음과 같이 방화벽 룰을 설정하였습니다.

![33](/KH_Security/방화벽/img/33.png)

### 룰 구성

| 순번 | Source | Service | Destination | 설명 |
|------|--------|---------|-------------|------|
| 1 | bas(main) | SSH | DMZ, Internal | Bastion Host를 통한 내부 접근 허용 |
| 2 | Any | HTTP/HTTPS | d_web | 외부 → 웹 서버 접근 |
| 3 | d_web | Any | in_oracle | 웹 서버 → DB 서버 |
| 4 | DMZ | Any | ex_main | DMZ → 외부 통신 |
| 5 | d_ids | SSH | in_oracle | IDS → DB 서버 |
| 6 | in_admin | SSH | d_web | 내부 관리자 → 웹 서버 |
| 7 | ex_main | SSH | d_ids | 외부 → IDS |

---

### 설정 목적

- 외부에서 내부망으로 직접 접근 차단하기 위해서입니다.
- 반드시 Bastion Host를 경유하도록 강제합니다.

---

##  MobaXterm SSH 접속 설정 (Bastion Host)

- 외부 환경에서 Bastion Host(d_web)에 SSH로 접속하기 위한 MobaXterm 세션 설정 화면입니다.
- 외부에서 내부망으로 들어가기 위한 첫 관문(Bastion Host) 접속 설정입니다.
- 비밀번호 입력 없이 SSH 키 기반 인증을 통해 서버에 접속할 수 있도록 설정하였습니다.

![34](/KH_Security/방화벽/img/34.png)

### 주요 설정 항목

| 항목 | 설정값 | 설명 |
|------|--------|------|
| Remote host | 192.168.12.11 | 접속 대상 서버 (Bastion Host) |
| Username | root | 접속 계정 |
| Port | 22 | SSH 기본 포트 |
| Use private key | E:\private key\jh.ppk | 키 기반 인증 설정 |

---

## SSH Jump Host 설정

- 외부에서 내부 서버로 직접 접근하지 않고,  
Bastion Host를 경유하여 접속할 수 있도록 SSH Jump Host를 설정하였습니다.

![35](/KH_Security/방화벽/img/35.png)

### 설정 정보

| 항목 | 값 |
|------|----|
| Gateway host | 192.168.11.19 |
| Username | root |
| Port | 22 |
| 인증 방식 | SSH Key (jh.ppk) |

---

### 접속 흐름

```text
내 PC → Bastion Host → 내부 서버
```

- 중간 서버를 통해 내부망 접근을 통제하는 구조입니다.

---

## SSH Jump Host 접속 확인

- SSH Jump Host 설정 이후, Gateway를 통해 Bastion Host에 정상적으로 접속되는 것을 확인하였습니다.

![36](/KH_Security/방화벽/img/36.png)

### 접속 정보

| 항목 | 값 |
|------|----|
| 최종 접속 서버 | 192.168.12.11 |
| Gateway | 192.168.11.19 |
| 접속 계정 | root |

---

### 접속 흐름

```text
내 PC → Gateway(192.168.11.19) → Bastion Host(192.168.12.11)
```

- Gateway를 통한 우회 접속이 정상적으로 동작함을 확인할 수 있습니다.
