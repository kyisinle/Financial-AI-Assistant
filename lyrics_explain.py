import sys
from time import sleep
import time 

def print_lyrics():
    lines = [
      
        #in below format. "---" is where the lyrics will be printed and besides it, is the length of time(seconds) to print out the lyrics
      
        ("", 0.01),
        
    ]
        #delays or sleep() , these are used when needed to take a pause when printing out sentence by sentence

    delays = [0.0]

    for i, (line, char_delay) in enumerate(lines):
        for char in line:
            print(char, end='')
          
          #calling sys. stdout. flush() forces it to “flush” the buffer, meaning that it will write everything in the buffer to the terminal, 
          #even if normally it would wait before doing so.
          
            sys.stdout.flush() 
            sleep(char_delay)
        time.sleep(delays[i])
        print('')

print_lyrics()
