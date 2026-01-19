# RPM & DNF

---

## RPM 명령어

```text
rpm  -qa : 시스템에 설치된 모든 패키지명
rpm  -qi  패키지명 :  패키지의 상세한 정보
rpm  -ql 패키지명 : 패키지의 파일 리스트
rpm  -qf 파일명 : 지정한 파일이 포함된 패키지
```

### rpm 추가 옵션 (주로 사용 X)

```text
--nodeps :  의존성을 무시하고 작업한다. (삭제에 주로 이용)
--force : 같은 버전의 패키지나 파일이 있어도 무시하고 작업진행
--oldpackage :  다운그레이드시 사
```

---

## rpm - 조회

- `rpm -qa`

![01]()

- `rpm -qi`

![02]()

- `rpm -ql`

![03]()  
![04]()

- `rpm -qf`

![05]()

---

## rpm - 설치 및 업그레이드

- 명령어
```text
rpm -ivh [패키지명] : 패키지가 아무것도 없을 때 설치
rpm -ivh [패키지명] : 패키지가 없을 시 설치 있으면 업데이트
rpm -ivh [패키지명] : 업데이트만 실시
```

### rpm - 설치 실습

```text
`rpm -UVh` 명령어를 이용하여 패키지를 설치 할 것입니다.  
그 전에 `mount`작업을 해줍니다.
```
![06]()

- `ls | grep vsftpd` 명령어를 통해 vsftpd 패키지가 있는 지 확인해줍니다.  
그리고 나서 `rpm -qa | grep` 명령어를 통해 패키지가 설치 되었는지 확인하고  
없으면 `rpm -Uvh` 명령어를 통해 설치 해줍니다.

![07]()

- 설치되는 모습을 볼 수 있습니다.

---

## rpm - 삭제

`rpm e [패키지명] 명령어를 통해 지정된 패키지를 삭제합니다.` 

![08]()

---

## DNF, YUM

### 개요 

- python을 기반으로 제작되었습니다.
- 대부분의 사용법이 yum과 호환됩니다.
- 8 이전 버전은 지원되지 않는다. – yum을 이용합니다.
- YUM의 기능을 개선한 명령으로 RHEL, Fedora, CentOS, AlmaLinux, Rocky Linux, Oracle Linux와 같은  
RPM 기반의 Linux 배포판 8 버전 이상에서 사용되는 패키지 매니저이다.  
YUM의 속도, 메모리 사용, 느린 의존성 확인 등의 문제를 개선했습니다.

---

## dnf - 조회

- `dnf list` : 패키지를 확인 하는 명령어 입니다.
- 설치 가능한 모든 패키지 목록을 보여줍니다. 

**dnf list [installed | updates | available | 패키지명]**

```text
installed : 설치된 패키지 목록을 보여줍니다.
updates : 업데이트된(가능한) 패키지 목록을 보여줍니다.
available : 설치 가능한 패키지 목록을 보여줍니다.
패키지명 : 패키지의 설치 여부와 update 정보를 보여줍니다.
```

---

## dnf - 검색 추가 옵션

- `repolist`, `search`, `repoquery`, `provide`

### dnf - 검색 추가 옵션 실습

#### dnf repolist

```text
dnf repolist
```
- 시스템에 등록된 repository list를 출력합니다.

![09]()

#### dnf search 문자열


```text
dnf search sql
```
- `sql`이라는 키워드를 포함한 패키지를 검색할 때 사용하는 명령어입니다.

![10]()

#### dnf repoquery -l 패키지

```text
dnf repoquery -l iproute
```
- iproute 패키지에 포함된 파일 목록을 확인할 때 사용합니다.  
아직 설치하지 않은 패키지라도 저장소(repo)에 있는 파일 구성까지 조회할 수 있다는 점이 핵심입니다.

![11]()

#### dnf provides 파일

```text
dnf provides /usr/sbin/ip
```
- /usr/sbin/ip 파일(명령어)을 제공하는 패키지가 무엇인지를 확인할 때 사용합니다.

![12]()

---

## dnf - 설치 및 업데이트

### dnf - 설치

- `# dnf install [-y] 패키지명`
- 패키지를 repository로 부터 설치합니다.

![13]()

---

### dnf - 업데이트

- `dnf update  [-y] 패키지명`
- 패키지를 repository로 부터 업데이트합니다.

- **주의할 점**  
서버가 작동이 안될 수 있기 때문에 업데이트는 대부분 하지 않습니다.
- 하는 경우
내가 사용하려는 기능이 안될 시에만 업데이트를 해주어야 합니다.

---

## dnf - 삭제

- 명령어
```text
# dnf remove [패키지명]
```

---

## dnf - 초기화

- 명령어
```text
# dnf clean all
```

---

## dnf group

- Group package 확인과 설치

- `dnf group [list | install "그룹" | remove "그룹" | info "그룹"]`
```text
list : 그룹 목록을 출력합니다.
install "그룹" : 그룹을 설치합니다.
remove "그룹" : 그룹을 제거합니다.
info "그룹" : 그룹의 정보를 검색합니다.
```
---

## dnf group - 목록 출력

`dnf group list`

![14]()

---

## dnf group - 설치

- 명령어
```text
dnf group install -y "그룹"
```

---

## Repository 관리

### 저장소 관리

- 명령어
```text
dnf repolist [all]
```
-  저장소 목록을 검색합니다.  
all : 모든 저장소 목록을 검색합니다.

![15]()

### 저장소 활성화

```text
dnf config-manager --set-enabled [repo] || \*
dnf config-manager --set-disabled [repo] || \*
```
- `--set-enabled [repo]` : 저장소 활성화
- `--set-disable [repo]` : 저장소 비활성화
- \*는 모든 repository를 의미 (추천하지 않습니다.)

![16]()

- 저장소 목록에 있는 rt 파일을 활성화 했다가 비활성화 해보았습니다.

---

## repository 추가

- 비활성화되어 있는 추가 저장소(Repository)를 활성화하는 데 사용됩니다.

- `RockyLinux` :  PowerTools
```text
dnf config-manager --set-enabled powertools
```

- `Oraclelinux` :  codeready-builder
```text
dnf config-manager --enable ol8_codeready_builder
```

### 기타 추가 저장소

- epel  
`dnf install -y epel-release`

- epel-next(RHEL 9 계열)  
`dnf install -y epel-next-release`

---
