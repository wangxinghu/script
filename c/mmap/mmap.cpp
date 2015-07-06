#include <sys/types.h>
#include <stdio.h>
#include <stdlib.h>
#include <string.h>
#include <sys/stat.h>
#include <fcntl.h>
#include <unistd.h>
#include <sys/mman.h>
int main(){
    int fd;
    void *start;
    struct stat sb;
    fd = open("./tt", O_RDWR, 777); /*打开file */
    fstat(fd, &sb); /* 取得文件大小 */
    start = mmap(NULL, sb.st_size, PROT_READ|PROT_WRITE, MAP_SHARED, fd, 0);
    if(start == MAP_FAILED) { /* 判断是否映射成功 */
        return 0;
    }
    printf("%s\n", start); 
    int temp = 100;
    char str[] = "after";
    memcpy(start, str, 5);
    printf("%s\n", start); 
    munmap(start, sb.st_size); /* 解除映射 */
    close(fd);
    return 0;
}
