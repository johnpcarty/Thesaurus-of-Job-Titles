import sys
import csv

def parse_line(line):
    raw_names, term = line.split('=>')
    names = raw_names.split(',')
    for name in names:
        yield [name.strip(), term.strip()]

def parse_file(file_name):
    with open(file_name, 'r') as input_file:
        for line in input_file:
            yield parse_line(line)

if __name__ == '__main__':
    input_file_name = sys.argv[1]
    output_file_name = sys.argv[2]

    with open(output_file_name, 'w') as output_file:
        writer = csv.writer(output_file)

        for pairs in parse_file(input_file_name):
            for pair in pairs:
                writer.writerow(list(pair))
