# File system, Partition

---

## 파일시스템 / 파티션 관련 명령어 정리


| 명령어 | 설명 | 예시 |
|---|---|---|
| `df` | 마운트된 파일시스템 용량 확인 | `df -Th` |
| `lsblk` | 디스크/파티션 구조 확인 | `lsblk` |
| `fdisk` | 파티션 생성/삭제/변경 | `fdisk /dev/sda` |
| `mkfs` | 파일시스템 생성 | `mkfs -t xfs /dev/sdb1` |
| `mkfs.xfs` | XFS 파일시스템 생성 | `mkfs.xfs -f /dev/sdb1` |
| `mkfs.ext4` | ext4 파일시스템 생성 | `mkfs.ext4 /dev/sda1` |
| `mount` | 파일시스템 마운트 | `mount /dev/sdb1 /home2` |
| `umount` | 파일시스템 언마운트 | `umount /home2` |
| `blkid` | UUID / LABEL 확인 | `blkid` |
| `findfs` | UUID/LABEL로 장치 검색 | `findfs UUID=xxxx` |
| `xfs_admin` | XFS 라벨 설정/확인 | `xfs_admin -L /home2 /dev/sdb1` |

---

## 디스크의 마운트 상태와 용량 확인

- 디스크의 마운트 상태를 보기위해 다음과 같은 명령어를 사용합니다.

```
df -Th
```

![01](/KH_Security/Linux/File%20System%20%26%20Partition/img/01.png)

- 여러 파일 시스템이 보이지만 추가한 `NVMe` 디스크는 아직 마운트되지 않은 상태임을 확인할 수 있습니다.

- 마운트란 디스크(or 파티션)에 존재하는 파일 시스템을 특정 디렉터리에 연결하여 파일처럼 접근할 수 있도록 만드는 과정입니다.

---

## 디바이스 파일 확인

- 추가된 NVMe 디스크가 디바이스 파일 디렉토리에서 어떤 이름으로 인식되는지 확인합니다.

```text
ls /dev/
```

![02](/KH_Security/Linux/File%20System%20%26%20Partition/img/02.png)

---

## Primary Partition 파티션 생성 (fdisk)

### 파티션 생성 시 명령어

| 명령 | 설명 |
|---|---|
| p | 현재 파티션 상태 출력 |
| d | 파티션 삭제 |
| n | 파티션 생성 |
| t | 파티션 타입 변경 |
| w | 저장 후 종료 |
| q | 저장하지 않고 종료 |

- 다음 명령어로 디스크 파티션 설정을 시작합니다.
```text
fdisk /dev/nvme0n2
```

![03](/KH_Security/Linux/File%20System%20%26%20Partition/img/03.png)

- `n`을 입력하여 파티션을 생성합니다.
- `Primary Partition`을 생성하기 위해 `p`를 입력합니다.
- 파티션 번호는 기본값 `1`을 사용합니다.
- 첫 번째 섹터는 기본값 `2048`을 사용합니다.
  - 디스크 정렬과 호환성을 보장하는 가장 안전한 시작 위치입니다.
- 마지막 섹터는 `+10GB`로 설정합니다.

---

## Extended Partition 파티션 생성 (fdisk)

![04](/KH_Security/Linux/File%20System%20%26%20Partition/img/04.png)

- `n`을 입력한 후 `e`를 선택하여 `Extended Partition`을 생성합니다.
- 파티션 번호는 자동으로 `2번`이 할당됩니다.
- 시작 섹터는 `Primary Partition` 이후로 자동 설정됩니다.
- `Extended Partition` 크기는 남은 10GB를 사용합니다.

---

## Logical Partition 생성

![05](/KH_Security/Linux/File%20System%20%26%20Partition/img/05.png)

- `p`를 입력하여 현재 파티션을 확인합니다.

![06](/KH_Security/Linux/File%20System%20%26%20Partition/img/06.png)

---

## 파일 시스템 생성

- 디스크를 사용하기 위해 파일 시스템을 생성합니다.
- 파일 시스템 생성에는 `mkfs` 명령어를 사용합니다.
- 필자는 `Primary Partition`에 `XFS` 파일 시스템을 생성합니다.
```text
mkfs -t xfs /dev/nvme0n2p1
mkfs.xfs [-f] /dev/nvme0n2p1
```
![07](/KH_Security/Linux/File%20System%20%26%20Partition/img/07.png)

- `mkfs -t xfs` 명령어는 `mkfs.xfs`와 동일한 의미입니다.
- 기존 파일 시스템을 덮어쓸 경우 `-f` 옵션이 필요할 수 있습니다.

---

## 마운트 디렉터리 생성 및 마운트

- 마운트 전에 파일 시스템을 연결할 빈 디렉터리를 생성합니다.
```text
mkdir /home1
```
- 다음 명령어로 파티션을 마운트합니다.
```text
mount /dev/nvme0n1p1 /home1
```
- df 명령어로 마운트 상태를 확인합니다.

![08](/KH_Security/Linux/File%20System%20%26%20Partition/img/08.png)

- Primary Partition `nvme0n1p1`이 정상적으로 마운트된 것을 확인할 수 있습니다.

### UUID를 사용한 마운트

- `blkid | grep nvme0n1p1` 으로 UUID값을 확인후 다음과 같이 mount를 하는 방법도 있습니다.

```text
mount UUID="22f108ee-51b9-4fb0-94a2-0129ea606a6a" /home1
```
- UUID로 mount를 하는 이유는 디바이스 이름(sda, sdb 등)이 바뀌어도 항상 같은 디스크를 정확히 마운트하기 위해서입니다.
- UUID는 디바이스에 대응되는 고유한 식별 문자열입니다.

### LABEL를 이용한 마운트

- `xfs_admin`

| 옵션 | 명령어 형식 | 설명 |
|---|---|---|
| 라벨 설정 | xfs_admin -L [라벨명] [장치명] | XFS 파일시스템에 새로운 라벨을 설정 |
| 라벨 삭제 | xfs_admin -L "--" [장치명] | 기존에 설정된 XFS 라벨을 제거 |
| 라벨 확인 | xfs_admin -l [장치명] | 해당 장치에 설정된 XFS 라벨을 출력 |

```text
xfs_admin-L /home1 /dev/nvme0n2p5

mount LABEL="/home1" /home1
```

---

## 언마운트 수행

- 언마운트는 파일 시스템을 디렉터리에서 분리하여 안전하게 제거하는 과정입니다.
- 먼저 현재 마운트 상태를 확인합니다.
```text
df
```
- 다음 명령어로 언마운트를 수행합니다.
```text
umount /dev/nvme0n2p1
```
![09](/KH_Security/Linux/File%20System%20%26%20Partition/img/09.png)

- `df` 출력 결과에서 해당 파티션이 제거된 것을 확인합니다.

---

## 언마운트 결과 확인

- 기존에 파일이 존재하던 디렉터리로 이동하여 파일이 보이지 않는 것을 확인합니다.
- 이는 파일 시스템이 정상적으로 언마운트되었음을 의미합니다.

![10](/KH_Security/Linux/File%20System%20%26%20Partition/img/10.png)

---
