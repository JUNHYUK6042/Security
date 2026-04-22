# Service 설정

## 개요

- service는 방화벽의 개별 항목을 목록화 해둔 것으로 서비스를 통해서 간단히 방화벽 설정을 추가 삭제 등 변경이 가능합니다.

- service는 생성 및 삭제가 가능하고 대부분의 항목들은 미리 제공된다.

- service에 대한 설정 내역은 `/usr/lib/firewalld/services/서비스명.xml`에서 확인이 가능합니다.

- 미리 정의된 서비스 목록 확인 및 현재 zone에 등록된 서비스를 확인합니다.

---

## Service 관련 명령어

| 옵션 | 예시 | 설명 |
|------|------|------|
| `--get-services` | firewall-cmd --get-services | 제공되는 전체 서비스 목록을 확인 |
| `--list-services` | firewall-cmd --list-services | 현재 zone에 등록된 서비스 목록을 확인 |
| `--list-services --zone=<zone>` | firewall-cmd --list-services --zone=public | 특정 zone의 서비스 목록을 확인 |
| `--add-service` | firewall-cmd --permanent --zone=public --add-service=http | zone에 서비스를 추가 |
| `--remove-service` | firewall-cmd --permanent --zone=public --remove-service=http | zone에서 서비스를 제거 |
| `--new-service` | firewall-cmd --permanent --new-service=myservice | 새로운 서비스를 생성 |
| `--delete-service` | firewall-cmd --permanent --delete-service=myservice | 서비스를 삭제 |

#### Service 속성 설정

| 옵션 | 예시 | 설명 |
|------|------|------|
| `--set-description` | firewall-cmd --permanent --service=myservice --set-description="설명" | 서비스 설명 설정 |
| `--set-short` | firewall-cmd --permanent --service=myservice --set-short="이름" | 서비스 이름 설정 |

#### Service 내부 설정

| 옵션 | 예시 | 설명 |
| ------ |------|------|
| `--add-port` | firewall-cmd --permanent --service=myservice --add-port=8080/tcp | 서비스에 포트 추가 |
| `--add-source-port` | firewall-cmd --permanent --service=myservice --add-source-port=1000-2000/tcp | 서비스에 소스 포트 추가 |
| `--set-destination` | firewall-cmd --permanent --service=myservice --set-destination=ipv4:1.1.1.1/32 | 서비스 목적지 설정 |

---

## Service 조회 및 확인

### 현재 zone 서비스 확인

- 다음 명령어는 현재 zone에 등록된 서비스 목록을 확인합니다.
```
firewall-cmd --list-services
```

![16](/KH_Security/Linux/Firewall/img/16.png)

- 출력 결과가 `ssh`이므로 현재 SSH 서비스만 허용된 상태입니다.

---

### 전체 서비스 목록 확인

- 다음 명령어는 전체 서비스 목록을 확인합니다.
```
firewall-cmd --get-services
```

![17](/KH_Security/Linux/Firewall/img/17.png)

- 출력된 값들은 사용 가능한 모든 서비스이며, 실제로 적용된 서비스는 아닙니다.

---

## Service 추가 및 삭제

### 특정 Zone Service 확인

![18](/KH_Security/Linux/Firewall/img/18.png)

- 현재 서비스가 `cockpit`, dhcpv6-client`, `ssh`가 있는 것을 확인할 수 있습니다.

---

### Service 변경 작업

- 다음 명령어로 특정 Service를 삭제합니다.
```
firewall-cmd --permanent --zone=public --remove-service cookpit

firewall-cmd --permanent --zone=public --remove-service dhcpv6-client
```

- 다음 명령어로 특정 Service를 추가합니다.
```
firewall-cmd --permanent --zone=public --add-service http
```

- 변경 후 `firewall-cmd --reload`를 하여 적용시킵니다.

---

### Servcie 변경 후 Service 확인

- 다음 명령어로 public zone에 등록된 서비스 목록을 확인합니다.
```
firewall-cmd --list-services --zone=public
```

![19](/KH_Security/Linux/Firewall/img/19.png)

- 출력 결과를 통해 현재 public zone에는 http와 ssh 서비스가 허용된 상태임을 확인할 수 있습니다.

---

### Zone 전체 설정 확인

- 다음 명령어로 현재 zone의 전체 방화벽 설정을 확인합니다.
```
firewall-cmd --list-all
```

![20](/KH_Security/Linux/Firewall/img/20.png)

- 출력 결과를 통해 public zone에서 http와 ssh 서비스가 허용되어 있고,  
  80/tcp 포트가 추가된 상태임을 확인할 수 있습니다.
- 또한 echo-request(ICMP)가 차단되어 ping 요청이 차단된 상태입니다.

---

## Service 생성 (oracle)

- 다음 명령어를 통해 Oracle Service를 생성 후 설정 파일을 확인합니다.
```
firewall-cmd --permanent --new-service=oracle // oracle 서비스를 새로 생성합니다.

firewall-cmd --permanent --service=oracle --set-short=oracle //oracle 서비스의 이름(short)을 설정합니다.

firewall-cmd --permanent --service=oracle --add-port=1521-1523/tcp //oracle 서비스에 사용할 포트(1521~1523/tcp)를 추가합니다.
```

### 설정 파일 확인

- 다음 명령어로 서비스 설정 파일을 확인합니다.
```
cat /etc/firewalld/services/oracle.xml
```

![21](/KH_Security/Linux/Firewall/img/21.png)

- 해당 설정은 oracle 서비스에 1521~1523 TCP 포트를 사용하는 서비스 정의를 생성한 것입니다.
- 즉, oracle 서비스를 추가하면 해당 포트 범위가 자동으로 허용됩니다.

---

### Service 적용 (oracle)

- 다음 명령어로 public zone에 oracle 서비스를 추가 및 적용합니다.
```
firewall-cmd --permanent --zone=public --add-service=oracle

firewall-cmd --reload //변경된 설정을 실제 방화벽에 적용합니다.
```

- 다음 명령어로 public zone에 적용된 서비스를 확인합니다.
```
firewall-cmd --list-services --zone=public
```

![22](/KH_Security/Linux/Firewall/img/22.png)

- 출력 결과를 통해 http, oracle, ssh 서비스가 허용된 상태임을 확인할 수 있습니다.

---

### 사용자 정의 Service 제거 및 삭제 (oracle)

- 다음 명령어로 public zone에서 oracle 서비스를 제거 후 실제 방화벽에 적용합니다.
```
firewall-cmd --permanent --zone=public --remove-service=oracle

firewall-cmd --reload
```

- 다음 명령어로 public zone에 적용된 서비스를 확인합니다.
```
firewall-cmd --list-services --zone=public
```

![23](/KH_Security/Linux/Firewall/img/23.png)

- 출력 결과를 통해 oracle 서비스가 제거되고 http, ssh만 허용된 상태임을 확인할 수 있습니다.

---

### 사용자 정의 Service 완전 삭제

- 다음 명령어로 생성했던 oracle 서비스를 시스템에서 완전히 삭제합니다.
```
firewall-cmd --permanent --delete-service oracle
```

- 다음 명령어로 서비스 XML 파일 삭제 여부를 확인합니다.
```
ls /etc/firewalld/services/oracle.xml
```

![24](/KH_Security/Linux/Firewall/img/24.png)

- 출력 결과에서 파일이 존재하지 않으면 정상적으로 삭제된 상태입니다.

---

### built-in Service 삭제 불가 확인

- 다음 명령어로 기본 제공 서비스(ftp)를 삭제 시도합니다.
```
firewall-cmd --permanent --delete-service ftp
```

![25](/KH_Security/Linux/Firewall/img/25.png)

- built-in service는 시스템 기본 서비스이므로 삭제가 불가능하며 에러가 발생합니다.

---

## 요약 정리

- service는 포트와 프로토콜을 묶은 방화벽 설정 단위입니다.
- `--add-service / --remove-service`로 zone에 서비스 추가 및 제거가 가능합니다.
- `--permanent` 옵션은 설정 저장이며, `--reload`를 해야 실제 적용됩니다.
- `--get-services`는 전체 목록, `--list-services`는 현재 적용 상태를 확인합니다.
- 사용자 정의 서비스는 생성(`--new-service`) 후 포트 설정하여 사용할 수 있습니다.
- service는 zone에 추가해야 실제 트래픽이 허용됩니다.
- `remove-service`는 zone에서 제거, `delete-service`는 서비스 자체 삭제입니다.
- built-in 서비스는 삭제가 불가능합니다.
