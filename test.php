<?php

    include "vendor/autoload.php";

    $sandBoxFactory = new \SandboxRE\Factory\SandboxFactory();
    $phpSandBox = $sandBoxFactory->make("php");
    $javascriptSandBox = $sandBoxFactory->make("javascript");
    $objectiveCSandBox = $sandBoxFactory->make("objectiveC");
    $javaSandBox = $sandBoxFactory->make("java");

    echo $javaSandBox->execute('

        public class Main
        {
            public static void main(String[] args) {
                System.out.println(\'Hello World!\');
            }
        }

    ') . PHP_EOL;
//
//    echo $objectiveCSandBox->execute('
//        #import <Foundation/Foundation.h>
//
//        @interface SampleClass:NSObject
//        /* method declaration */
//        - (int)max:(int)num1 andNum2:(int)num2;
//        @end
//
//        @implementation SampleClass
//
//        /* method returning the max between two numbers */
//        - (int)max:(int)num1 andNum2:(int)num2{
//        /* local variable declaration */
//           int result;
//
//           if (num1 > num2)
//           {
//              result = num1;
//           }
//           else
//           {
//              result = num2;
//           }
//
//           return result;
//        }
//
//        @end
//
//        int main ()
//        {
//           /* local variable definition */
//           int a = 300;
//           int b = 500;
//           int ret;
//
//           SampleClass *sampleClass = [[SampleClass alloc]init];
//
//           /* calling a method to get max value */
//           ret = [sampleClass max:a andNum2:b];
//
//           printf("%d\n", ret );
//
//           return 0;
//        }
//    ') . PHP_EOL;
//
    echo $javascriptSandBox->execute('
        calculate(120, 130, [15, 17, 18, 18, 19]);

        function calculate(k, jump, styles) {
          var distDifference = jump - k;
          var startPoints = 60;
          var distPoints = distDifference > 0 ? (startPoints + 2 * distDifference) : (startPoints + distDifference);

          var max = Math.max(...styles);
          var min = Math.min(...styles);

          styles.splice(styles.indexOf(max), 1);
          styles.splice(styles.indexOf(min), 1);

          var stylesSum = 0;

          for (var i = 0; i < styles.length; i++) {
              stylesSum += styles[i];
          }

          return stylesSum + distPoints;
        }
    ') . PHP_EOL;

    echo $phpSandBox->execute('
        function mostlyUsed($numbers)
        {
            $operatorsCount = [
                "Operator A" => 0,
                "Operator B" => 0,
                "Operator C" => 0
            ];

            foreach ($numbers as $number) {
                if( in_array($number[0].$number[1].$number[2], ["070", "071", "072"])) {
                    $operatorsCount["Operator A"]++;
                    continue;
                }

                if( in_array($number[0].$number[1].$number[2], ["075", "076"])) {
                    $operatorsCount["Operator B"]++;
                    continue;
                }

                if( in_array($number[0].$number[1].$number[2], ["077", "078"])) {
                    $operatorsCount["Operator C"]++;
                    continue;
                }
            }

            return array_keys($operatorsCount, max($operatorsCount))[0];
        }

        echo mostlyUsed(["071222-333", "077111-222", "072123-123", "075321-321"]); // Operator A
    '). PHP_EOL;


echo $phpSandBox->execute('
        function add($a, $b) {
            return $a + $b;
        }
        
        echo add(10, 5);
    '). PHP_EOL;
