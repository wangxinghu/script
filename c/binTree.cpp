#include <cstdlib>
#include <iostream>
#include <stack>

using namespace std;

//二叉树定义 
typedef char ElementType;

typedef struct BiTreeNode
{
	ElementType data;
    struct BiTreeNode* lchild;
    struct BiTreeNode* rchild;
}BiTreeNode, *BiTree;


//递归的建立一棵二叉树 
//输入为二叉树的先序序列 
void createBiTree(BiTree &T)
{
	char data;
	data = getchar();
	if(data == '#')
	{
		T = NULL;
	}
	else
	{
		T = new BiTreeNode;
		T->data = data;
		createBiTree(T->lchild);
		createBiTree(T->rchild);
	}
}

//通过广义表建立二叉树 
void createBiTreeWithGenList(BiTree &T)
{
	stack<BiTree> s;//存放待输入孩子的结点 
	BiTree p = NULL;//用于生成新的结点
	int k = 0;//记录期待的结点, k==1表示期待左孩子结点，k==2期待右孩子结点
	char ch = getchar();
	
	//处理根结点 
	if(ch!='#')
	{
		p = new BiTreeNode;
		p->data = ch;
		p->lchild = NULL;
		p->rchild = NULL;
		T = p;//根结点 
	}
	while((ch=getchar())!='#')
	{
		switch(ch)
		{
			case '(':
				s.push(p);//上一个生成的结点，即p入栈，p有孩子 
				k = 1;	//下一个插入的应为左孩子结点 
				break;
			case ',':
				k = 2;	//下一个插入的应为右孩子结点 
				break;
			case ')':
				s.pop();//结点完成孩子的输入，出栈 
				break;
			default:
				p = new BiTreeNode;
				p->data = ch;
				p->lchild = NULL;
				p->rchild = NULL;
				if(k==1)
					s.top()->lchild = p;
				else 
					s.top()->rchild = p;
		}		
	}
}

//以广义表的方式输出二叉树
void printBiTreeWithGenList(const BiTree&T)
{
	if(T)
	{
		cout<<T->data;
		if(T->lchild ||T->rchild)//左右子树不全空 
		{
			cout<<"(";
			printBiTreeWithGenList(T->lchild);//递归输出左子树 ，可能为空 
			if(T->rchild)		
			{
				cout<<",";
				printBiTreeWithGenList(T->rchild);
			}
			cout<<")";
		}
	}
}
 
//递归销毁一棵二叉树
void destroyBiTree(BiTree &T)
{
	if(T)
	{
		destroyBiTree(T->lchild);
		destroyBiTree(T->rchild);
		delete T;
		T = NULL;
	}
} 

//递归先序遍历二叉树 
void preOrderTraverse(const BiTree &T)
{
	if(T)
	{
		cout<<T->data<<" ";//输出根节点值 
		preOrderTraverse(T->lchild);//遍历左子树 
		preOrderTraverse(T->rchild);//遍历右子树 
	}
}

//递归中序遍历二叉树
void inOrderTraverse(const BiTree &T)
{
	if(T)
	{
		inOrderTraverse(T->lchild);//遍历左子树 
		cout<<T->data<<" ";//输出根节点值 
		inOrderTraverse(T->rchild);//遍历右子树 
	}
}

//递归后序遍历二叉树
void postOrderTraverse(const BiTree &T)
{
	if(T)
	{
		postOrderTraverse(T->lchild);//遍历左子树 
		postOrderTraverse(T->rchild);//遍历右子树 
		cout<<T->data<<" ";//输出根节点值 
	} 
}

//递归求树的深度 
int depthOfBiTree(const BiTree &T)
{
	int ldepth;
	int rdepth;
	
	if(T==NULL)//空树 
		return 0;
	ldepth = depthOfBiTree(T->lchild);
	rdepth = depthOfBiTree(T->rchild);
	
	return (ldepth>rdepth)?(ldepth+1):(rdepth+1);
}

//递归求二叉树的叶子结点个数
int leafCountOfBiTree(const BiTree &T)
{	
	if(T==NULL)
		return 0;
	if(T->lchild==NULL && T->rchild==NULL)
		return 1;
	return leafCountOfBiTree(T->lchild) + leafCountOfBiTree(T->rchild);
} 

//递归交换二叉树的左右子女
void exchangeChild(BiTree &T)
{
	if(T)
	{
		BiTree temp = NULL;
		
		if(T->lchild ||T->rchild)
		{
			temp = T->lchild;
			T->lchild = T->rchild;
			T->rchild = temp;
			exchangeChild(T->lchild);
			exchangeChild(T->rchild);
		}
	}
}
 
int main(int argc, char *argv[])
{
	BiTree T = NULL;
	
	createBiTree(T);//建立二叉树 如输入AB#D##CE### 
//	createBiTreeWithGenList(T);//如输入A(B(,D),C(E))#
	
	cout<<"preOrder: "; //先序遍历 
	preOrderTraverse(T);
	cout<<endl;
	
	cout<<"inOrder: ";//中序遍历 
	inOrderTraverse(T);
	cout<<endl;
	
	cout<<"postOrder: ";//后序遍历 
	postOrderTraverse(T);
	cout<<endl;
	
	cout<<"depth: "<<depthOfBiTree(T)<<endl;//树的高度 
	
	cout<<"the count of leaf: "<<leafCountOfBiTree(T)<<endl;//叶子结点数 
	
	cout<<"The tree after exchange: ";
	exchangeChild(T);
	printBiTreeWithGenList(T);
	
	destroyBiTree(T);//销毁二叉树，释放空间 
	
    return 0;
}