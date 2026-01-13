# VI Editor

- **vi editor는 리눅스/유닉스 계열에서 기본으로 제공되는 텍스트 편집기로, 키보드 중심의 조작과 모드 기반 동작 방식이 특징다.**

---

## VI Editor 실행
- Vi Editor는 vi [파일명] 명령어를 통해 실행합니다.

![01](/KH_Security/Linux/Vi%20Editor/img/01.png)
- 파일이 존재하지 않는 경우, vi는 새 파일을 생성

![02](/KH_Security/Linux/Vi%20Editor/img/02.png)
- 하단에 `"a.txt" [New File]` 이 표시되며, 이는 새 파일이 생성되었음을 의미함

---

## Insert 모드
- 처음 상태는 명령모드이다.
- 명령모드에서 i를 입력시 Insert Mode로 바뀐다.
- i : 커서 위치부터 입력 가능
- o : 커서 아래 새로운 라인을 삽입하고 입력 가능

![03](/KH_Security/Linux/Vi%20Editor/img/03.png)

- Insert모드 전환 시 문자열을 입력할 수 있다.

---

## 파일 저장 및 종료

- Insert 모드에서 **명령 모드로 돌아가기 위해 `Esc` 키**를 누릅니다.
- 명령 모드에서 사용할 수 있는 기본 명령은 다음과 같습니다.

  - `:wq` → 저장 후 종료
  - `:q!` → 저장하지 않고 강제 종료

- 저장하기 위해 `:wq` 입력해 주었습니다.

![04](/KH_Security/Linux/Vi%20Editor/img/04.png)

---

## 파일 저장되어 있는 내용 출력

![05](/KH_Security/Linux/Vi%20Editor/img/05.png)

- 저장하지 않고 종료하려면 `:q!`를 입력해 주면 됩니다.

---

## 줄 번호 표시

- 명령 모드에서 `:set nu` 명령어를 입력해 주면 좌측에 번호를 표시합니다.

![06](/KH_Security/Linux/Vi%20Editor/img/06.png)
![07](/KH_Security/Linux/Vi%20Editor/img/07.png)
