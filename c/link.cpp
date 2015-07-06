#include<stdio.h>
#include<stdlib.h>

typedef struct node
{
    int data;
    node* pNext;
}Node;

//链表的操作，以有头节点为例，无头节点类似
Node* head = NULL;

//创建链表，头结点data=0，pNext=NULL;
bool createNodeList()
{
    head = (Node*) malloc(sizeof(Node));
    if(NULL == head)
    {
        return false;
    }
    else
    {
        head->data = 0;
        head->pNext = NULL;
        return true;
    }
}

//增加节点
bool addNode(Node* node)
{
    if(NULL == head)
    {
        return false;
    }
    Node* p = head->pNext;
    Node* q = head;
    while(NULL != p)
    {
        q = p;
        p = p->pNext;
    }
    q->pNext = node;
    node->pNext = NULL;
    return true;    
}

//删除节点
bool deleteNode(int index)
{
    if(NULL == head)
    {
        return false;
    }
    Node* p = head->pNext;

    int length = 0;
    while(NULL != p)
    {
        length ++;
        p = p->pNext;
    }

    if(length < index)
    {
        return false;
    }
    else
    {
        Node* q = head;
        p = head;
        for(int i=0;i<index;i++)
        {
            q = p;
            p = p->pNext;
        }
        Node* t = p->pNext;
        q->pNext = t;
        free(p);
        return true;
    }
}

//逆序
void reverseNodeList()
{
    if(NULL == head)
    {
        return;
    }
    //如果链表长度为1
    if(head->pNext == NULL)
    {
        return;
    }
    Node* p = head->pNext;
    Node* q = p->pNext;
    Node* t = NULL;
    while(NULL != q)
    {
        t = q->pNext;
        q->pNext = p;
        p = q;
        q = t;
    }
    head->pNext->pNext = NULL;
    head->pNext = p;
}

//排序(降序)
void sort()
{
    //冒泡排序
    Node* pHead = head;
    if(head == NULL)
    {
        return;
    }
    if(pHead->pNext == NULL)
    {
        return;
    }
    Node* pi = pHead->pNext;
    Node* pj = pi->pNext;
    for(;pi != NULL;pi=pi->pNext)
    {
        for(pj = pi->pNext;pj != NULL;pj=pj->pNext)
        {
            if(pj->data>pi->data)
            {
                int tmp = pj->data;
                pj->data = pi->data;
                pi->data = tmp;
            }
        }
    }
}
//销毁
void destroyNodeList()
{
    if(NULL == head)
    {
        return;
    }
    if(NULL == head->pNext)
    {
        free(head);
        head = NULL;
        return;
    }
    Node* p = head->pNext;
    while(NULL != p)
    {
        Node* tmp = p;
        p = p->pNext;
        free(tmp);
    }
    free(head);
    head = NULL;
}

int main()
{
    createNodeList();

    Node* node1 = (Node*)malloc(sizeof(Node));
    node1->data = 1;
    node1->pNext = NULL;

    Node* node2 = (Node*)malloc(sizeof(Node));
    node2->data = 2;
    node2->pNext = NULL;

    addNode(node1);
    addNode(node2);

    reverseNodeList();

    Node* node3 = (Node*)malloc(sizeof(Node));
    node3->data = 3;
    node3->pNext = NULL;

    addNode(node3);

    sort();

    deleteNode(2);

    destroyNodeList();
    return 0;
}
