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

![16]()

- 출력 결과가 `ssh`이므로 현재 SSH 서비스만 허용된 상태입니다.

---

### 전체 서비스 목록 확인

- 다음 명령어는 전체 서비스 목록을 확인합니다.
```
firewall-cmd --get-services
```

![17]()

- 출력된 값들은 사용 가능한 모든 서비스이며, 실제로 적용된 서비스는 아닙니다.

---

## Service 추가 및 삭제

### 특정 Zone Service 확인

![18]()

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

![19]()

- 출력 결과를 통해 현재 public zone에는 http와 ssh 서비스가 허용된 상태임을 확인할 수 있습니다.

---

### Zone 전체 설정 확인

- 다음 명령어로 현재 zone의 전체 방화벽 설정을 확인합니다.
```
firewall-cmd --list-all
```

![20]()

- 출력 결과를 통해 public zone에서 http와 ssh 서비스가 허용되어 있고, 80/tcp 포트가 추가된 상태임을 확인할 수 있습니다.
- 또한 echo-request(ICMP)가 차단되어 ping 요청이 차단된 상태입니다.

---

x
