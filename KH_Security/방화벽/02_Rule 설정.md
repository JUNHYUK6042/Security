# SOPHOS 방화벽 룰 설정

## 개요

- 본 실습에서는 SOPHOS 방화벽을 이용하여 DMZ, Internal, External 네트워크 간 통신을 제어하는  
방화벽 룰(ACL)을 설정합니다.

- 방화벽은 내부망과 외부망을 분리하고  
허용된 트래픽만 통과시키는 보안 장비입니다.

---

## 방화벽 룰 설정 원칙

- 룰은 **위에서 아래 순서대로 적용됩니다.**
- 가능한 `Any` 대신 **명확한 객체(Source / Service / Destination)**를 사용합니다.
- **External → Internal 직접 접근은 금지**합니다.
- 반드시 필요한 통신만 허용하는 **최소 권한 원칙**을 적용합니다.

---

## 방화벽 룰 구성 요소

| 항목 | 설명 |
|------|------|
| Source | 출발지 (누가) |
| Service | 서비스/포트 (무엇을) |
| Destination | 목적지 (어디로) |
| Action | 허용/차단 |

---

## 실제 설정된 방화벽 룰

- 다음 화면은 방화벽 룰을 설정하기 위한 초기 화면입니다.
- 본 설정은 내부 네트워크(Internal) 사용자가 외부 인터넷을 이용할 수 있도록  
기본 서비스들을 허용하는 방화벽 정책입니다.

![25](/KH_Security/방화벽/img/25.png)

---

- 룰을 추가하기위해 왼쪽 New Rule 버튼을 클릭하면 다음과 같은 화면이 나옵니다.
- 다음 화면은 새로운 방화벽 룰을 생성하여 특정 트래픽을 허용(Allow) 또는 차단(Deny)하는 설정 화면입니다.

![26](/KH_Security/방화벽/img/26.png)

---

### 포트 포워딩(Port Forwarding) 설정

- 외부에서 내부 서버에 접근하기 위해 포트 포워딩을 설정합니다.

![27](/KH_Security/방화벽/img/27.png)

---

## Rule 목록

- 외부 사용자의 웹 서비스 접근을 허용하고, 웹 서버를 통해서만  
  내부 DB 서버에 접근할 수 있도록 하기 위해 다음과 같이 방화벽 룰을 설정하였습니다.

![28](/KH_Security/방화벽/img/28.png)

| 순번 | Source | Service | Destination | 설명 |
|---|---|---|---|---|
| 1 | Any | HTTP, HTTPS | d_web | 외부 사용자의 웹 서버 접근 허용 |
| 2 | d_web | Oracle SQL*Net | in_oracle | 웹 서버 → DB 서버 연결 |
| 3 | DMZ(Network) | Any | ex_main | DMZ → 외부 통신 허용 |
| 4 | d_ids | SSH | in_oracle | IDS → Oracle 관리 접속 |
| 5 | in_admin | SSH | d_web | 내부 관리자 → 웹 서버 접속 |
| 6 | ex_main | SSH | d_ids | 외부 → IDS 접속 |

### 트래픽 흐름

```text
외부 사용자 → 웹 서버(DMZ) → DB 서버(Internal)
```

---

### 핵심 구조 설명

- 웹 서버(d_web)는 외부에 공개됨 (HTTP/HTTPS)
- DB 서버(in_oracle)는 내부망에서만 접근 가능
- 관리 및 운영 접속은 SSH로 제한
- 네트워크는 External → DMZ → Internal 구조로 분리됨
---

## 핵심 룰 해석

### Web 서비스 공개

- `Any → d_web (HTTP/HTTPS)`
- 외부 사용자가 웹 서버에 접속 가능

- DMZ에 웹 서버를 두는 이유는 내부망 보호를 위해서입니다.

---

### Web → DB 연결

- `d_web → in_oracle (1523 포트)`
- Oracle Listener 포트 허용

- 이 룰이 없으면 웹 서비스 DB 연결이 불가능합니다.

---

### 관리용 SSH 접근

- 내부 관리자 → 웹 서버
- IDS → DB 서버
- 외부 → IDS

---

## Oracle 포트 변경 및 SSH 접속 흐름

- 기본 Oracle Listener 포트(1521)를 1523으로 변경하고,   
방화벽 및 SSH를 통해 DB 서버에 접속하는 실습입니다.

### 방화벽 서비스 설정

- 다음과 같은 화면은 Oracle 통신을 위한 포트를 허용합니다.

![29](/KH_Security/방화벽/img/29.png)

---

### Oracle listener.ora 설정
```sql
LISTENER =
 (DESCRIPTION_LIST =
  (DESCRIPTION =
   (ADDRESS = (PROTOCOL = TCP)(HOST = DB19.itclass.co.kr)(PORT = 1523))
   (ADDRESS = (PROTOCOL = IPC)(KEY = EXTPROC1521))
  )
 )
```

![30](/KH_Security/방화벽/img/30.png)

- 변경후 `lsnrctl stop`, `lsnrctl start` 명령어를 통해
  변경한 설정을 실제로 적용해줍니다.

---

### tnsnames.ora 설정
```sql
DB19 =
 (DESCRIPTION =
  (ADDRESS = (PROTOCOL = TCP)(HOST = DB19.itclass.co.kr)(PORT = 1523))
  (CONNECT_DATA =
   (SERVER = DEDICATED)
   (SERVICE_NAME = DB19.itclass.co.kr)
  )
 )

ORACLE =
 (DESCRIPTION =
  (ADDRESS = (PROTOCOL = TCP)(HOST = 192.168.0.11)(PORT = 1523))
  (CONNECT_DATA = (SID = DB19))
 )
```

![31](/KH_Security/방화벽/img/31.png)

---

## SSH 접속 흐름

### 접속 과정

```bash
ssh root@192.168.12.11   # 웹 서버 (DMZ)

ssh root@192.168.0.11    # DB 서버 (Internal)
```

![32](/KH_Security/방화벽/img/32.png)

- SSH 원격 접속을 통해 대상 서버에 정상적으로 접속되는 것을 확인할 수 있습니다.
